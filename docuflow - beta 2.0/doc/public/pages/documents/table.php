<?php
if (!isset($_SESSION['uid']) && !$_SESSION['uid'] == true) {
  define('PUBLIC_PATH', $_SERVER['DOCUMENT_ROOT'] . '/doc/public');
  header('Location:' . PUBLIC_PATH . '/index.php');
  exit();
}
?>

<div class="table-container">
  <div class="filters-panel">
    <div class="filters-row">
      <div class="filter-group grow">
        <label data-i18n="quickSearch">Quick Search</label>
        <input type="text" id="searchInput" class="search-input" placeholder="Search documents..."
          data-i18n-placeholder="search">
      </div>
      <div class="filter-group">
        <label data-i18n="advancedOptions">Advanced</label>
        <button class="btn" id="toggleAdvanced" type="button" data-i18n="showAdvanced">Show Advanced</button>
      </div>
    </div>
    <div class="filters-advanced" id="advancedFilters">
      <div class="filters-row">
        <div class="filter-group">
          <label data-i18n="searchField">Search Field</label>
          <select id="searchField" class="filter-select">
            <option value="all" data-i18n="searchAll">All Fields</option>
            <option value="customer_name" data-i18n="documentName">Name</option>
            <option value="national_number" data-i18n="documentNN">NN</option>
            <option value="phone" data-i18n="documentPhone">Phone</option>
            <option value="passport" data-i18n="documentPn">Passport</option>
            <option value="contact" data-i18n="labelContact">Contact</option>
            <option value="status" data-i18n="labelStatus">Status</option>
            <option value="iban" data-i18n="labelIbm">IBAN</option>
            <option value="notes" data-i18n="labelNotes">Notes</option>
            <option value="created_at" data-i18n="documentCreated">Date</option>
          </select>
        </div>
        <div class="filter-group">
          <label data-i18n="matchMode">Match</label>
          <select id="matchMode" class="filter-select">
            <option value="contains" data-i18n="matchContains">Contains</option>
            <option value="starts" data-i18n="matchStarts">Starts With</option>
            <option value="ends" data-i18n="matchEnds">Ends With</option>
            <option value="exact" data-i18n="matchExact">Exact</option>
          </select>
        </div>
      </div>
      <div class="filters-row">
        <div class="filter-group">
          <label data-i18n="dateFrom">Date From</label>
          <input type="date" id="dateFrom" class="filter-input">
        </div>
        <div class="filter-group">
          <label data-i18n="timeFrom">Time From</label>
          <input type="time" id="timeFrom" class="filter-input">
        </div>
        <div class="filter-group">
          <label data-i18n="dateTo">Date To</label>
          <input type="date" id="dateTo" class="filter-input">
        </div>
        <div class="filter-group">
          <label data-i18n="timeTo">Time To</label>
          <input type="time" id="timeTo" class="filter-input">
        </div>
        <div class="filter-group checkbox-group">
          <label for="groupByDay">
            <input type="checkbox" id="groupByDay">
            <span data-i18n="groupByDay">Group By Day</span>
          </label>
        </div>
      </div>
    </div>
    <div class="bulk-actions">
      <div class="bulk-left">
        <label class="bulk-select">
          <input type="checkbox" id="selectAllPage">
          <span data-i18n="selectAllPage">Select all on page</span>
        </label>
        <span class="bulk-count" id="selectedCount">0</span>
        <span data-i18n="selectedLabel">selected</span>
      </div>
      <div class="bulk-right">
        <button class="btn btn-delete" id="bulkDelete" data-i18n="bulkDelete">Delete Selected</button>
        <button class="btn btn-view" id="bulkExport" data-i18n="bulkExport">Send to Automation</button>
      </div>
    </div>
  </div>
  <table id="documentsTable">
    <thead>
      <tr>
        <th><input type="checkbox" id="selectAll" /></th>
        <th data-i18n="documentId">Id</th>
        <!-- <th data-i18n="document-Title">Title</th> -->
        <th data-i18n="documentName">Name</th>
        <th data-i18n="documentNN" class="mob-m">NN</th>
        <th data-i18n="documentPn" class="mob-m tab-m">Pn</th>
        <th data-i18n="documentPhone" class="mob-m">phone</th>
        <th data-i18n="documentCreated">Data</th>
        <th data-i18n="actions">Actions</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td colspan="8">wite</td>
      </tr>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="8">
          <div class="pagination-container">
            <span data-i18n="showing">Showing 10 of 0</span>
            <div class="page-size">
              <label data-i18n="itemsPerPage">Items per page</label>
              <select id="itemsPerPageSelect">
                <option value="10">10</option>
                <option value="13" selected>13</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="all" data-i18n="all">All</option>
              </select>
            </div>
            <div class="pagination" style="display:flex; flex-direction: row;">
              <button class="btn" id="prevBtn" onclick="changePage('prev')"><i class="bi bi-arrow-left"></i></button>
              <div class="page-numbers" id="pageNumbers">
                <!-- أرقام الصفحات ستتم إضافتها ديناميكياً -->
              </div>
              <button class="btn" id="nextBtn" onclick="changePage('next')"><i class="bi bi-arrow-right"></i></button>
            </div>
            <button class="btn primary" data-i18n="newUpload" onclick="window.location.href='/doc/public/pages/documents/upload.php'">
              New Upload
            </button>
          </div>
        </td>
      </tr>
    </tfoot>
  </table>
</div>
