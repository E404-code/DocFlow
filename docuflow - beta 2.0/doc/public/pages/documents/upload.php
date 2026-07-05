<?php

define('ASSETS_PATH', '/doc/public/assets');

define('PUBLIC_PATH', $_SERVER['DOCUMENT_ROOT'] . '/doc/public');

session_start();

$page = "crud";

if (!isset($_SESSION['uid']) && !$_SESSION['uid'] == true) {

  header('Location: /doc/public/index.php');

  exit();

}



if ($_SERVER['REQUEST_METHOD'] === 'GET') {

  $imagePath = '/doc/uploads/documents/';

  function getmod($type)
  {

    $doc_id = htmlspecialchars(trim($_GET[$type])) ?? '';
    $my_id = htmlspecialchars(trim($_SESSION['uid'])) ?? '';

    try {

      require_once PUBLIC_PATH . '/../config/database.php';

      $query = "
      WITH RECURSIVE doc_by_group AS(
      SELECT id, name, group_id FROM users WHERE id = ?
    
      UNION ALL
      SELECT u.id, u.name, u.group_id FROM users u
      INNER JOIN doc_by_group g ON u.group_id = g.id
      )
      SELECT d.*, u.name created_by, u.role FROM documents d INNER JOIN doc_by_group g ON d.user_id = g.id INNER JOIN users u ON d.user_id = u.id WHERE d.id = ? AND d.is_delete = 'false';
      ";
      $fetch = $pdo->prepare($query);
      $fetch->execute([$my_id, $doc_id]);
      $data = $fetch->fetch();
      if ($data) {
        $query = "SELECT a.action, a.created_at, u.name, u.role FROM activity_log a INNER JOIN users u ON a.user_id = u.id WHERE a.document_id = ? ORDER BY a.created_at ASC;";
        $ac = $pdo->prepare($query);
        $ac->execute([$doc_id]);
        $actions = $ac->fetchAll();
        $data[] = [...$actions];

        return $data;
      }

    } catch (Exception $e) {

      throw $e;

    }

  }



  if (isset($_GET['view']) && $_GET['view']) {

    $document = getmod('view');

    if ($document) {

      $mod = 'view';

      $disabled = 'disabled';

      $display = 'none';

    } else {

      // المستند غير موجود - عرض صفحة الرفع فقط

      $mod = 'upload';

      $disabled = '';

      $display = '';

    }

  } else if (isset($_GET['edit']) && $_GET['edit']) {

    $document = getmod('edit');

    if ($document) {

      $mod = 'edit';

      $disabled = '';

      $display = '';

    } else {

      // المستند غير موجود - عرض صفحة الرفع فقط

      $mod = 'upload';

      $disabled = '';

      $display = '';

    }

  } else {

    $mod = 'upload';

    $disabled = '';

    $display = '';

  }



}

?>

<!DOCTYPE html>

<html lang="en">



<head>

  <meta charset="utf-8" />

  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>DocuFlow - Enterprise CRUD</title>

  <link rel="icon" type="image/png" href="<?= ASSETS_PATH . '/img/favicon/favicon.png' ?>">

  <link rel="stylesheet" href="<?= ASSETS_PATH . '/css/global.css' ?>">

  <link rel="stylesheet" href="<?= ASSETS_PATH . '/css/dashboard.css' ?>">

  <link rel="stylesheet" href="<?= ASSETS_PATH . '/css/documents-upload.css' ?>">

  <link rel="stylesheet" href="<?= ASSETS_PATH . '/css/notifications.css' ?>">

</head>



<body data-theme="light">

  <div class="layout">

    <?php require_once PUBLIC_PATH . '/components/aside.php' ?>



    <main>

      <div class="topbar">

        <div class="top-actions">
          <?php if ($mod === 'view' || $mod === 'edit'): ?>
            <button class="btn toggle-mode-btn" onclick="toggleEditMode()">
              <?php if ($mod === 'view'): ?>
                <span data-i18n="editMode">Edit Mode</span>
              <?php else: ?>
                <span data-i18n="viewMode">View Mode</span>
              <?php endif; ?>
            </button>
          <?php endif; ?>
          <button class="btn" onclick="toggleLang()">AR / EN</button>

          <button class="btn" onclick="toggleTheme()">☀ / 🌙</button>

        </div>

      </div>

      <div class="form-box">

        <form id="customerForm">

          <?php if ($mod === 'edit'): ?>

            <input type="hidden" name="id" value="<?= $document['id'] ?>">

          <?php endif; ?>



          <fieldset>

            <legend data-i18n="personalInfo">Personal Information</legend>



            <div class="field">

              <label for="customerName" data-i18n="labelName">

                Name of Customer (Required)

              </label>

              <input type="text" id="customerName" name="customerName" placeholder="Ahmed Mohamed Ali"
                data-i18n-placeholder="placeholderlabelName" <?php if ($mod === 'view' || $mod === 'edit') {

                  echo $disabled;

                  echo ' value="' . $document['customer_name'] . '"';

                } ?> required>

            </div>



            <div class="user-info">

              <div class="field">

                <label for="nationalNumber" data-i18n="labelNN">

                  National Number (Required)

                </label>

                <input type="text" id="nationalNumber" name="nationalNumber" inputmode="numeric"
                  placeholder="220098000660" <?php if ($mod === 'view' || $mod === 'edit') {

                    echo $disabled;

                    echo ' value="' . $document['national_number'] . '"';

                  } ?> required>

              </div>



              <div class="field">

                <label for="phone" data-i18n="labelPhone">

                  Phone Number (Required)

                </label>

                <input type="tel" data-i18n-placeholder="placeholdertel" id="phone" name="phone"
                  placeholder="0905611770" <?php if ($mod === 'view' || $mod === 'edit') {

                    echo $disabled;

                    echo ' value="' . $document['phone'] . '"';

                  } ?> required>

              </div>



              <div class="field">

                <label for="passport" data-i18n="labelPassport">

                  Passport (Required)

                </label>

                <input type="text" id="passport" name="passport" placeholder="AD678989" <?php if ($mod === 'view' || $mod === 'edit') {

                  echo $disabled;

                  echo ' value="' . $document['passport'] . '"';

                } ?> required>

              </div>

            </div>

          </fieldset>



          <fieldset>

            <legend data-i18n="documentInfo">Document Info</legend>



            <div class="field">

              <label for="contact" data-i18n="labelContact">

                Contact (Required)

              </label>

              <input type="text" data-i18n-placeholder="contactplaceholder" id="contact" name="contact"
                placeholder="Name or 0940607710" <?php if ($mod === 'view' || $mod === 'edit') {

                  echo $disabled;

                  echo ' value="' . $document['contact'] . '"';

                } ?> required>

            </div>



            <div class="field">

              <label for="Price" data-i18n="labelPrice">

                Price (Required)

              </label>

              <input type="number" data-i18n-placeholder="Priceplaceholder" id="Price" name="Price"
                placeholder="1500د.ل" <?php if ($mod === 'view' || $mod === 'edit') {

                  echo $disabled;

                  echo ' value="' . $document['price'] . '"';

                } ?> required>

            </div>



            <div class="field">

              <label for="status" data-i18n="labelStatus">

                Status (Optional)

              </label>

              <select name="status" id="status" <?php if ($mod === 'view' || $mod === 'edit') {

                echo $disabled;

                $option_select = $document['status'];

              } ?>>

                <option <?= isset($option_select) && $option_select === "new" ? "selected" : "" ?> value="new"
                  data-i18n="statusNew">New</option>

                <option <?= isset($option_select) && $option_select === "w-resv" ? "selected" : "" ?> value="w-resv"
                  data-i18n="statusWaiting">Waiting

                  Reservation</option>

                <option <?= isset($option_select) && $option_select === "on-resv" ? "selected" : "" ?> value="on-resv"
                  data-i18n="statusOn">On

                  Reservation</option>

                <option <?= isset($option_select) && $option_select === "enough" ? "selected" : "" ?> value="enough"
                  data-i18n="statusEnough">Enough

                </option>

                <option <?= isset($option_select) && $option_select === "pending-delivery" ? "selected" : "" ?>
                  value="pending-delivery" data-i18n="statusPendingDelivery">Pending Delivery</option>

                <option <?= isset($option_select) && $option_select === "delivered" ? "selected" : "" ?> value="delivered"
                  data-i18n="statusDelivered">

                  Delivered</option>

              </select>

            </div>



            <div class="field">

              <label for="ibm" data-i18n="labelIbm">

                IBAN (Optional)

              </label>

              <input type="text" id="ibm" name="ibm" placeholder="LY****************" <?php if ($mod === 'view' || $mod === 'edit') {

                echo $disabled;

                echo ' value="' . ($document['iban'] === '' ? 'بدون IBAM' : $document['iban']) . '"';

              } ?>>

            </div>



            <div class="field">

              <label for="notes" data-i18n="labelNotes">

                Notes (Optional)

              </label>

              <input type="text" id="notes" name="notes" placeholder="no sms card" <?php if ($mod === 'view' || $mod === 'edit') {

                echo $disabled;

                echo ' value="' . ($document['notes'] === '' ? 'بدون ملاحظة' : $document['notes']) . '"';

              } ?>>

            </div>

          </fieldset>



          <fieldset>

            <legend data-i18n="images">Document Images</legend>



            <div class="field">

              <label for="passportImage" data-i18n="labelPassportImage">

                Passport Image <?= $mod === 'edit' ? '(Optional - Leave empty to keep current)' : '(Required)' ?>

              </label>

              <input type="file" id="passportImage" name="passportImage" accept="image/*" <?php if ($mod === 'view') {

                echo 'style="display:' . $display . '"';

              } elseif ($mod === 'edit') {

                echo '';

              } else {

                echo 'required';

              } ?>>

              <?php if ($mod === 'view') {

                if (file_exists($_SERVER['DOCUMENT_ROOT']  . $imagePath . $document['passport_image'])) {

                  echo '<img src="' . $imagePath . $document['passport_image'] . '" title="Passport Image" class="image-preview">';

                  echo '<input type="number" name="id" style="display:none" value="' . $document['id'] . '" ></input> ';

                  echo '<button class="btn download-btn">Download</button>';

                } else {

                  echo "<p style='display:block;'>بدون صورة</p>";

                }

              } elseif ($mod === 'edit' && !empty($document['passport_image'])) { 

                if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath . $document['passport_image'])) :
                ?>
                  
                <div class="current-image" style="margin-top: 10px;">

                  <img src="<?= $imagePath . $document['passport_image'] ?>"
                    style="max-width: 100px; height: auto; border: 1px solid #ddd; border-radius: 4px;">

                  <p style="font-size: 0.875rem; color: #666; margin: 5px 0;">Current image - Leave empty to keep it</p>

                  <input type="hidden" name="old_passport_image" value="<?= $document['passport_image'] ?>">

                </div>

              <?php else : echo "<p style='display:block;'>بدون صورة</p>"; endif;} ?>

            </div>



            <div class="field">

              <label for="nnImage" data-i18n="labelNNImage">

                National Number Image <?= $mod === 'edit' ? '(Optional - Leave empty to keep current)' : '(Required)' ?>

              </label>

              <input type="file" id="nnImage" name="nnImage" accept="image/*" <?php if ($mod === 'view') {

                echo 'style="display:' . $display . '"';

              } elseif ($mod === 'edit') {

                echo '';

              } else {

                echo 'required';

              } ?>>

              <?php if ($mod === 'view') {

                if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath . $document['nn_image'])) {

                  echo '<img src="' . $imagePath . $document['nn_image'] . '" title="National Number Image" class="image-preview">

                        <button class="btn download-btn">Download</button>';

                } else {

                  echo "<p style='display:block;'>بدون صورة</p>";

                }

              } elseif ($mod === 'edit' && !empty($document['nn_image'])) { 
                if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath . $document['nn_image'])) : ?>

                <div class="current-image" style="margin-top: 10px;">

                  <img src="<?= $imagePath . $document['nn_image'] ?>"
                    style="max-width: 100px; height: auto; border: 1px solid #ddd; border-radius: 4px;">

                  <p style="font-size: 0.875rem; color: #666; margin: 5px 0;">Current image - Leave empty to keep it</p>

                  <input type="hidden" name="old_nn_image" value="<?= $document['nn_image'] ?>">

                </div>

              <?php else : echo "<p style='display:block;'>بدون صورة</p>"; endif;} ?>

            </div>

          </fieldset>

          <div class="form-actions">

            <button type="reset" class="btn reset-btn" data-i18n="reset" <?= $mod === 'view' ? 'style="display: none"' : '' ?>>

              Reset

            </button>

            <button type="submit" class="btn submit-btn" data-i18n="<?= $mod === 'view' ? 'update' : 'submit' ?>"
              <?= $mod === 'view' ? 'style="display:' . $display . '"' : '' ?>>

              Send

            </button>

          </div>

        </form>
        <?php if ($mod === 'view'): ?>
          <!-- Additional Information Section -->
          <div class="additional-info">
            <h3 data-i18n="additionalInfo">More Infor</h3>

            <div class="document-meta">
              <div class="meta-item">
                <span class="meta-label" data-i18n="uploadDate">Data Upload:</span>
                <span class="meta-value"><?= date('Y-m-d H:i', strtotime($document['created_at'])) ?></span>
              </div>
              <div class="meta-item">
                <span class="meta-label" data-i18n="uploadedBy">uploaded by:</span>
                <span class="meta-value"><?= ($document['created_by'] ?? 'غير معروف') ?>
                  (<?= $document['role'] ?? 'غير محدد' ?>)</span>
              </div>
            </div>

            <div class="timeline">
              <h4 data-i18n="activityHistory">سجل التعديلات</h4>

              <?php if (!empty($document[0])): ?>
                <?php foreach ($document[0] as $activity): ?>
                  <div class="timeline-item">
                    <div
                      class="timeline-dot <?= $activity['action'] === 'create_document' ? 'created' : ($activity['action'] === 'update_document' ? 'updated' : 'edited') ?>">
                    </div>
                    <div class="timeline-content">
                      <div class="timeline-action">
                        <?php
                        switch ($activity['action']) {
                          case 'create_document':
                            echo '<span data-i18n="documentCreated">تم الإنشاء</span>';
                            break;
                          case 'update_document':
                            echo '<span data-i18n="documentUpdated">تحديث المستند</span>';
                            break;
                          default:
                            echo '<span data-i18n="dataUpdated">تعديل البيانات</span>';
                            break;
                        }
                        ?>
                      </div>
                      <div class="timeline-user"><?= $activity['name'] ?? 'نظام' ?>
                        (<?= $activity['role'] ?? 'System' ?>)</div>
                      <div class="timeline-date"><?= date('Y-m-d H:i', strtotime($activity['created_at'])) ?></div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <!-- Fallback to static data if no activities exist -->
                <div class="timeline-item">
                  <div class="timeline-dot created"></div>
                  <div class="timeline-content">
                    <div class="timeline-action" data-i18n="documentCreated">تم الإنشاء</div>
                    <div class="timeline-user"><?= $document['created_by'] ?? 'غير معروف' ?>
                      (<?= $document['role'] ?? 'غير محدد' ?>)</div>
                    <div class="timeline-date"><?= date('Y-m-d H:i', strtotime($document['created_at'])) ?></div>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>

      </div>


    </main>

  </div>

  <script src="<?= ASSETS_PATH ?>/js/translations.js"></script>
  <script src="<?= ASSETS_PATH ?>/js/theme.js"></script>
  <script src="<?= ASSETS_PATH ?>/js/notifications.js"></script>
  <script src="<?= ASSETS_PATH ?>/js/uploads.js"></script>
</body>

</html>