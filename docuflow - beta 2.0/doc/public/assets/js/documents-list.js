const tbody = document?.querySelector("tbody");
const showing = document?.querySelector('[data-i18n="showing"]');
const pageNumbers = document?.getElementById("pageNumbers");
const prevBtn = document?.getElementById("prevBtn");
const nextBtn = document?.getElementById("nextBtn");
const selectAllHeader = document?.getElementById("selectAll");
const selectAllPage = document?.getElementById("selectAllPage");
const selectedCount = document?.getElementById("selectedCount");
const bulkDelete = document?.getElementById("bulkDelete");
const bulkExport = document?.getElementById("bulkExport");
const itemsPerPageSelect = document?.getElementById("itemsPerPageSelect");

const searchInput = document?.getElementById("searchInput");
const searchField = document?.getElementById("searchField");
const matchMode = document?.getElementById("matchMode");
const dateFrom = document?.getElementById("dateFrom");
const dateTo = document?.getElementById("dateTo");
const timeFrom = document?.getElementById("timeFrom");
const timeTo = document?.getElementById("timeTo");
const groupByDay = document?.getElementById("groupByDay");
const toggleAdvanced = document?.getElementById("toggleAdvanced");
const advancedFilters = document?.getElementById("advancedFilters");

let currentPage = 1;
let itemsPerPage = 13;
let totalItems = 0;
let totalPages = 0;
let allDocuments = [];
let filteredDocuments = [];
let selectedIds = new Set();
let searchDebounce = null;

function showNotification(message, type = "info") {
  const existingNotification = document.querySelector(".notification");
  if (existingNotification) {
    existingNotification.remove();
  }
  const notification = document.createElement("div");
  notification.className = `notification notification-${type}`;
  notification.textContent = message;
  notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 1000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;

  if (type === "success") {
    notification.style.backgroundColor = "#16a34a";
  } else if (type === "error") {
    notification.style.backgroundColor = "#dc2626";
  } else {
    notification.style.backgroundColor = "#2563eb";
  }

  document.body.appendChild(notification);

  setTimeout(() => {
    notification.style.transform = "translateX(0)";
  }, 100);

  setTimeout(() => {
    notification.style.transform = "translateX(100%)";
    setTimeout(() => {
      notification.remove();
    }, 300);
  }, 3000);
}

async function documentsFetch(url) {
  const request = await fetch(url, { method: "POST" });
  return await request.json();
}

async function initDocuments() {
  try {
    const listOfDocument = await documentsFetch("/doc/api/get_documents.php");
    allDocuments = listOfDocument.document || [];
    applyFilters();
  } catch (e) {
    console.error(e);
  }
}

function normalizeText(text) {
  if (!text) return "";
  let value = String(text).toLowerCase();
  value = value.replace(/[\u064B-\u065F\u0670\u0640]/g, "");
  value = value.replace(/[إأآا]/g, "ا");
  value = value.replace(/[ى]/g, "ي");
  value = value.replace(/[ة]/g, "ه");
  value = value.replace(/[ؤ]/g, "و");
  value = value.replace(/[ئ]/g, "ي");
  value = value.replace(/[ء]/g, "");
  value = value.replace(/[^0-9a-z\u0600-\u06FF]+/gi, "");
  return value;
}

function matchText(target, query, mode) {
  if (!query) return true;
  const t = normalizeText(target);
  const q = normalizeText(query);
  if (!q) return true;
  if (mode === "starts") return t.startsWith(q);
  if (mode === "ends") return t.endsWith(q);
  if (mode === "exact") return t === q;
  return t.includes(q);
}

function parseDocDate(value) {
  if (!value) return null;
  const iso = String(value).replace(" ", "T");
  const date = new Date(iso);
  if (Number.isNaN(date.getTime())) return null;
  return date;
}

function buildDateTime(dateValue, timeValue, isEnd = false) {
  if (!dateValue) return null;
  const time = timeValue || (isEnd ? "23:59" : "00:00");
  const dt = new Date(`${dateValue}T${time}:00`);
  if (Number.isNaN(dt.getTime())) return null;
  return dt;
}

function filterDocument(doc, filters) {
  const query = filters.query;
  const field = filters.field;
  const mode = filters.mode;

  if (query) {
    if (field === "all") {
      const fields = [
        doc.customer_name,
        doc.national_number,
        doc.phone,
        doc.passport,
        doc.contact,
        doc.status,
        doc.iban,
        doc.notes,
        doc.created_at,
      ];
      const anyMatch = fields.some((f) => matchText(f, query, mode));
      if (!anyMatch) return false;
    } else if (!matchText(doc[field], query, mode)) {
      return false;
    }
  }

  if (filters.startDate || filters.endDate) {
    const docDate = parseDocDate(doc.created_at);
    if (!docDate) return false;
    if (filters.startDate && docDate < filters.startDate) return false;
    if (filters.endDate && docDate > filters.endDate) return false;
  }

  return true;
}

function applyFilters() {
  const filters = {
    query: searchInput?.value?.trim() || "",
    field: searchField?.value || "all",
    mode: matchMode?.value || "contains",
    startDate: buildDateTime(dateFrom?.value, timeFrom?.value, false),
    endDate: buildDateTime(dateTo?.value, timeTo?.value, true),
  };

  filteredDocuments = allDocuments.filter((doc) =>
    filterDocument(doc, filters),
  );
  totalItems = filteredDocuments.length;
  totalPages = Math.ceil(totalItems / getItemsPerPage());

  selectedIds = new Set(
    [...selectedIds].filter((id) =>
      filteredDocuments.some((d) => String(d.id) === String(id)),
    ),
  );
  currentPage = Math.min(currentPage, totalPages || 1);
  displayCurrentPage();
  updatePaginationButtons();
  updateSelectionUI();
}

function getItemsPerPage() {
  if (itemsPerPageSelect?.value === "all") return filteredDocuments.length || 1;
  return itemsPerPage;
}

function displayCurrentPage() {
  if (!tbody) return;
  tbody.innerHTML = "";

  if (!filteredDocuments.length) {
    tbody.innerHTML = `<tr><td colspan="8" style="text-align: center; padding: 20px; color: #666;">${t("noSearchResults", "No matching documents found")}</td></tr>`;
    if (showing) showing.textContent = "Showing 0 of 0";
    return;
  }

  const perPage = getItemsPerPage();
  const startIndex = (currentPage - 1) * perPage;
  const endIndex = Math.min(startIndex + perPage, filteredDocuments.length);
  const currentDocuments = filteredDocuments.slice(startIndex, endIndex);

  if (groupByDay?.checked) {
    renderGroupedRows(currentDocuments);
  } else {
    renderRows(currentDocuments);
  }

  if (showing) {
    const startItem = filteredDocuments.length > 0 ? startIndex + 1 : 0;
    showing.textContent = `Showing ${startItem}-${endIndex} of ${filteredDocuments.length}`;
  }
}

function renderRows(documents) {
  let documentsList = "";
  documents.forEach((list, i) => {
    const globalIndex = filteredDocuments.indexOf(list);
    const checked = selectedIds.has(String(list.id)) ? "checked" : "";
    documentsList += `<tr>
      <td><input type="checkbox" class="row-check" data-id="${list.id}" ${checked} /></td>
      <td>${globalIndex + 1}</td>
      <td>${list.customer_name || ""}</td>
      <td class="mob-m">${list.national_number || ""}</td>
      <td class="mob-m tab-m">${list.passport || ""}</td>
      <td class="mob-m">${list.phone || ""}</td>
      <td>${list.created_at || ""}</td>
      <td>
        <button class="btn btn-edit" title="Edit" onclick="btnActions(${globalIndex},'edit');"><i class="bi bi-pencil-square"></i></button>
        <button class="btn btn-view" title="View" onclick="btnActions(${globalIndex},'view');"><i class="bi bi-arrow-up-right-square"></i></button>
        <button class="btn btn-delete" title="delete" onclick="docunetDelete(${list.id})";><i class="bi bi-trash"></i></button>
      </td>
    </tr>`;
  });

  tbody.innerHTML =
    documentsList || '<tr><td colspan="8">No documents found</td></tr>';
  bindRowCheckboxes();
}

function renderGroupedRows(documents) {
  let documentsList = "";
  let currentGroup = "";
  documents.forEach((list) => {
    const date = parseDocDate(list.created_at);
    const groupKey = date ? date.toISOString().slice(0, 10) : "unknown";
    if (groupKey !== currentGroup) {
      currentGroup = groupKey;
      documentsList += `<tr class="group-row"><td colspan="8">${groupKey}</td></tr>`;
    }
    const globalIndex = filteredDocuments.indexOf(list);
    const checked = selectedIds.has(String(list.id)) ? "checked" : "";
    documentsList += `<tr>
      <td><input type="checkbox" class="row-check" data-id="${list.id}" ${checked} /></td>
      <td>${globalIndex + 1}</td>
      <td>${list.customer_name || ""}</td>
      <td class="mob-m">${list.national_number || ""}</td>
      <td class="mob-m tab-m">${list.passport || ""}</td>
      <td class="mob-m">${list.phone || ""}</td>
      <td>${list.created_at || ""}</td>
      <td>
        <button class="btn btn-edit" title="Edit" onclick="btnActions(${globalIndex},'edit');"><i class="bi bi-pencil-square"></i></button>
        <button class="btn btn-view" title="View" onclick="btnActions(${globalIndex},'view');"><i class="bi bi-arrow-up-right-square"></i></button>
        <button class="btn btn-delete" title="delete" onclick="docunetDelete(${list.id})";><i class="bi bi-trash"></i></button>
      </td>
    </tr>`;
  });
  tbody.innerHTML = documentsList;
  bindRowCheckboxes();
}

function bindRowCheckboxes() {
  document.querySelectorAll(".row-check").forEach((checkbox) => {
    checkbox.addEventListener("change", function () {
      const id = this.dataset.id;
      if (this.checked) {
        selectedIds.add(String(id));
      } else {
        selectedIds.delete(String(id));
      }
      updateSelectionUI();
    });
  });
  updateSelectionUI();
}

function updateSelectionUI() {
  if (selectedCount) {
    selectedCount.textContent = selectedIds.size;
  }
  const perPage = getItemsPerPage();
  const startIndex = (currentPage - 1) * perPage;
  const endIndex = Math.min(startIndex + perPage, filteredDocuments.length);
  const currentDocuments = filteredDocuments.slice(startIndex, endIndex);
  const allSelected =
    currentDocuments.length > 0 &&
    currentDocuments.every((doc) => selectedIds.has(String(doc.id)));

  if (selectAllHeader) selectAllHeader.checked = allSelected;
  if (selectAllPage) selectAllPage.checked = allSelected;
}

function toggleSelectAllOnPage(checked) {
  const perPage = getItemsPerPage();
  const startIndex = (currentPage - 1) * perPage;
  const endIndex = Math.min(startIndex + perPage, filteredDocuments.length);
  const currentDocuments = filteredDocuments.slice(startIndex, endIndex);

  currentDocuments.forEach((doc) => {
    if (checked) selectedIds.add(String(doc.id));
    else selectedIds.delete(String(doc.id));
  });

  displayCurrentPage();
  updateSelectionUI();
}

function updatePaginationButtons() {
  if (!pageNumbers) return;
  pageNumbers.innerHTML = "";
  totalPages = Math.ceil(filteredDocuments.length / getItemsPerPage());

  let startPage = Math.max(1, currentPage - 2);
  let endPage = Math.min(totalPages, startPage + 4);

  if (endPage - startPage < 4 && startPage > 1) {
    startPage = Math.max(1, endPage - 4);
  }

  if (startPage > 1) {
    addPageButton(1);
    if (startPage > 2) {
      const dots = document.createElement("span");
      dots.textContent = "...";
      dots.style.padding = "0 10px";
      pageNumbers.appendChild(dots);
    }
  }

  for (let i = startPage; i <= endPage; i++) {
    addPageButton(i);
  }

  if (endPage < totalPages) {
    if (endPage < totalPages - 1) {
      const dots = document.createElement("span");
      dots.textContent = "...";
      dots.style.padding = "0 10px";
      pageNumbers.appendChild(dots);
    }
    addPageButton(totalPages);
  }

  if (prevBtn) prevBtn.disabled = currentPage === 1;
  if (nextBtn) nextBtn.disabled = currentPage === totalPages;
}

function addPageButton(pageNum) {
  const button = document.createElement("button");
  button.className = "btn";
  button.textContent = pageNum;
  button.onclick = () => goToPage(pageNum);

  if (pageNum === currentPage) {
    button.style.backgroundColor = "#007bff";
    button.style.color = "white";
  }

  pageNumbers.appendChild(button);
}

function changePage(direction) {
  if (direction === "prev" && currentPage > 1) {
    currentPage--;
  } else if (direction === "next" && currentPage < totalPages) {
    currentPage++;
  }
  displayCurrentPage();
  updatePaginationButtons();
}

function goToPage(pageNum) {
  currentPage = pageNum;
  displayCurrentPage();
  updatePaginationButtons();
}

async function btnActions(index, action) {
  const document = filteredDocuments[index]?.id;
  if (!document) return;
  window.location.href =
    "/doc/public/pages/documents/upload.php?" + action + "=" + document;
}

async function docunetDelete(index) {
  try {
    const request = await fetch("/doc/api/document_delete.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id: index }),
    });
    if (!request.ok) {
      showNotification("Delete failed", "error");
      return;
    }
    const response = await request.json();
    if (response.status === "error") {
      showNotification(response.message, "error");
    }
    if (response.status === "success") {
      showNotification(response.message, "success");
      removeDocumentById(index);
    }
  } catch (e) {
    console.log(e);
  }
}

function removeDocumentById(id) {
  allDocuments = allDocuments.filter((doc) => String(doc.id) !== String(id));
  filteredDocuments = filteredDocuments.filter(
    (doc) => String(doc.id) !== String(id),
  );
  selectedIds.delete(String(id));
  totalItems = filteredDocuments.length;
  totalPages = Math.ceil(totalItems / getItemsPerPage());
  if (currentPage > totalPages && totalPages > 0) {
    currentPage = totalPages;
  }
  displayCurrentPage();
  updatePaginationButtons();
  updateSelectionUI();
}

async function bulkDeleteSelected() {
  if (!selectedIds.size) {
    showNotification(t("noDocumentsSelected", "No documents selected"), "info");
    return;
  }
  if (!confirm(t("confirmDeleteSelected", "Delete selected documents?")))
    return;

  const ids = Array.from(selectedIds);
  for (const id of ids) {
    await docunetDelete(id);
  }
  selectedIds.clear();
  updateSelectionUI();
}

async function bulkExportSelected() {
  if (!selectedIds.size) {
    showNotification(t("noDocumentsSelected", "No documents selected"), "info");
    return;
  }
  try {
    const request = await fetch("/doc/api/automation_bridge.php", {
      method: "POST",
      body: JSON.stringify({ ids: Array.from(selectedIds) }),
    });
    const response = await request.json();
    if (response.status === "success") {
      showNotification(
        response.message || t("automationSuccess", "Sent to automation"),
        "success",
      );
    } else {
      showNotification(
        response.message || t("automationFailed", "Automation failed"),
        "error",
      );
    }
  } catch (e) {
    showNotification(t("automationFailed", "Automation failed"), "error");
  }
}

function bindFilterEvents() {
  if (searchInput) {
    searchInput.addEventListener("input", function () {
      clearTimeout(searchDebounce);
      searchDebounce = setTimeout(applyFilters, 300);
    });
  }
  [searchField, matchMode, dateFrom, dateTo, timeFrom, timeTo].forEach((el) => {
    if (el) el.addEventListener("change", applyFilters);
  });
  if (groupByDay) {
    groupByDay.addEventListener("change", displayCurrentPage);
  }
  if (toggleAdvanced && advancedFilters) {
    toggleAdvanced.addEventListener("click", function () {
      const isOpen = advancedFilters.classList.toggle("open");
      this.textContent = t(
        isOpen ? "hideAdvanced" : "showAdvanced",
        isOpen ? "Hide Advanced" : "Show Advanced",
      );
    });
  }
}

function bindSelectionEvents() {
  if (selectAllHeader) {
    selectAllHeader.addEventListener("change", function () {
      toggleSelectAllOnPage(this.checked);
    });
  }
  if (selectAllPage) {
    selectAllPage.addEventListener("change", function () {
      toggleSelectAllOnPage(this.checked);
    });
  }
  if (bulkDelete) {
    bulkDelete.addEventListener("click", bulkDeleteSelected);
  }
  if (bulkExport) {
    bulkExport.addEventListener("click", bulkExportSelected);
  }
}

function bindPaginationEvents() {
  if (itemsPerPageSelect) {
    itemsPerPageSelect.addEventListener("change", function () {
      const value = this.value;
      itemsPerPage =
        value === "all" ? filteredDocuments.length || 1 : parseInt(value, 10);
      currentPage = 1;
      displayCurrentPage();
      updatePaginationButtons();
    });
  }
}

function t(key, fallback) {
  try {
    if (
      typeof translations !== "undefined" &&
      translations[currentLang] &&
      translations[currentLang][key]
    ) {
      return translations[currentLang][key];
    }
  } catch (e) {
    // ignore
  }
  return fallback;
}

bindFilterEvents();
bindSelectionEvents();
bindPaginationEvents();
initDocuments();
