<?php
// pages/admin/adminCustomer.php
require_once __DIR__ . '/../../_base.php';
require_once __DIR__ . '/../../controller/customer-controller.php';

// processing operation
$action = get('action');

// remove individual
if ($action == 'delete' && get('id')) {

    // ❌ Prohibition of deleting oneself
    if ($currentUser && get('id') === $currentUser->user_id) {
        flash('error', 'You cannot delete your own account.');
        redirect('adminCustomer.php');
    }

    CustomerController::handleDelete(get('id'));
}

// Batch Remove
if ($action == 'bulk_delete' && is_post()) {
    $selectedIds = post('selected_ids');
    $currentUser = getCurrentUser();

    if (!empty($selectedIds) && is_array($selectedIds)) {

        // ❌ If it includes oneself, remove it directly
        if ($currentUser) {
            $selectedIds = array_filter($selectedIds, function ($id) use ($currentUser) {
                return $id !== $currentUser->user_id;
            });
        }

        if (empty($selectedIds)) {
            flash('error', 'You cannot delete your own account.');
            redirect('adminCustomer.php');
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($selectedIds as $id) {
            if (CustomerDAO::deleteCustomer($id)) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        if ($successCount > 0) {
            flash('success', "Successfully deleted $successCount user(s).");
        }
        if ($failCount > 0) {
            flash('error', "Failed to delete $failCount user(s).");
        }

        redirect('adminCustomer.php');
    }
}


if ($action == 'update' && is_post()) {
    CustomerController::handleUpdate();
}

if ($action == 'create' && is_post()) {
    CustomerController::handleCreate();
}

// Obtain search and filter parameters
$search = get('search');
$status = get('status', 'All');
$sortBy = get('sort', 'user_id');
$roleFilter = get('role', 'Member');

// paging logic
$limit = 13;
$page = (int)get('page', 1);
if ($page < 1) $page = 1;

$offset = ($page - 1) * $limit;
$startNum = $offset + 1;

$queryParams = http_build_query([
    'search' => $search,
    'status' => $status,
    'sort'   => $sortBy,
    'role'   => $roleFilter,
    'page'   => $page
]);

$totalCustomers = CustomerDAO::getTotalCount($search, $status, $roleFilter);
$totalPages = ceil($totalCustomers / $limit);
$customers = CustomerDAO::getAllCustomers($search, $status, $sortBy, $roleFilter, $limit, $offset);

if ($totalCustomers == 0) {
    $startNum = 0;
    $endNum = 0;
} else {
    $startNum = $offset + 1;
    $endNum = min($offset + count($customers), $totalCustomers);
}

$selectedCustomer = null;
if (get('edit')) {
    $selectedCustomer = CustomerDAO::getCustomerById(get('edit'));
}

$successMsg = flash('success');
$errorMsg = flash('error');

include 'adminHeader.php';
?>
<link rel="stylesheet" href="../../css/adminCustomer.css">

<div class="customers-page">
    
    <?php if ($successMsg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($successMsg) ?></div>
    <?php endif; ?>
    
    <?php if ($errorMsg): ?>
        <div class="alert alert-error"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>
    
    <div class="customers-header">
        <div>
            <h1>Users Management</h1>
            <p>Manage all user accounts (Members and Admins).</p>
            <button class="add-btn" onclick="showAddModal()">
                <span>＋</span> Add User
            </button>
            <!-- Batch Delete button -->
            <button class="bulk-delete-btn" id="bulkDeleteBtn" onclick="bulkDelete()" style="display:none;">
                <span>🗑</span> Delete Selected
            </button>
        </div>
    </div>

    <div class="customers-content">
        <div class="left-section">
            <!-- Search + Filters -->
            <form method="GET" action="adminCustomer.php" class="customer-toolbar">
                <div class="search-box">
                    <svg width="16" height="16" fill="#64748b" viewBox="0 0 24 24">
                        <path d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z"/>
                    </svg>
                    <input type="text" name="search" placeholder="Search by name, email, or ID..." 
                           value="<?= htmlspecialchars($search) ?>">
                </div>

                <select name="role" class="filter-select" onchange="this.form.submit()">
                    <option value="Member" <?= $roleFilter == 'Member' ? 'selected' : '' ?>>Role: Member</option>
                    <option value="Admin" <?= $roleFilter == 'Admin' ? 'selected' : '' ?>>Role: Admin</option>
                </select>

                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="All" <?= $status == 'All' ? 'selected' : '' ?>>Status: All</option>
                    <option value="Unblock" <?= $status == 'Unblock' ? 'selected' : '' ?>>Unblock</option>
                    <option value="Unverify" <?= $status == 'Unverify' ? 'selected' : '' ?>>Unverify</option>
                    <option value="Blocked" <?= $status == 'Blocked' ? 'selected' : '' ?>>Blocked</option>
                </select>

                <select name="sort" class="sort-select" onchange="this.form.submit()">
                    <option value="user_id" <?= $sortBy == 'user_id' ? 'selected' : '' ?>>Sort by: ID</option>
                    <option value="user_name" <?= $sortBy == 'user_name' ? 'selected' : '' ?>>Sort by: Name</option>
                    <option value="email" <?= $sortBy == 'email' ? 'selected' : '' ?>>Sort by: Email</option>
                    <option value="account_status" <?= $sortBy == 'account_status' ? 'selected' : '' ?>>Sort by: Status</option>
                </select>
            </form>

            <!-- Table -->
            <div class="customer-table-container">
                <table class="customer-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($customers)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 30px;">
                                    No customers found
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($customers as $customer): ?>
                            <tr id="row-<?= htmlspecialchars($customer->user_id) ?>"
                                class="<?= ($selectedCustomer && $selectedCustomer->user_id == $customer->user_id) ? 'selected' : '' ?>">
                                <td onclick="event.stopPropagation();">
                                    <input type="checkbox" class="user-checkbox" 
                                           value="<?= htmlspecialchars($customer->user_id) ?>"
                                           onchange="updateBulkDeleteBtn()">
                                </td>
                                <td onclick="window.location.href='?edit=<?= $customer->user_id ?>&<?= $queryParams ?>'" 
                                    style="cursor: pointer;">
                                    <?= htmlspecialchars($customer->user_id) ?>
                                </td>
                                <td onclick="window.location.href='?edit=<?= $customer->user_id ?>&<?= $queryParams ?>'" 
                                    style="cursor: pointer;"
                                    class="<?= ($selectedCustomer && $selectedCustomer->user_id == $customer->user_id) ? 'highlight' : '' ?>">
                                    <?= htmlspecialchars($customer->user_name) ?>
                                </td>
                                <td onclick="window.location.href='?edit=<?= $customer->user_id ?>&<?= $queryParams ?>'" 
                                    style="cursor: pointer;">
                                    <?= htmlspecialchars($customer->email) ?>
                                </td>
                                <td onclick="window.location.href='?edit=<?= $customer->user_id ?>&<?= $queryParams ?>'" 
                                    style="cursor: pointer;">
                                    <?= htmlspecialchars($customer->contact_num ?? 'N/A') ?>
                                </td>
                                <td onclick="window.location.href='?edit=<?= $customer->user_id ?>&<?= $queryParams ?>'" 
                                    style="cursor: pointer;">
                                    <?php
                                    $statusClass = strtolower($customer->account_status);
                                    if ($statusClass == 'unblock') $statusClass = 'active';
                                    if ($statusClass == 'blocked') $statusClass = 'suspended';
                                    if ($statusClass == 'unverify') $statusClass = 'new';
                                    ?>
                                    <span class="status <?= $statusClass ?>">
                                        <?= htmlspecialchars($customer->account_status) ?>
                                    </span>
                                </td>
                                <td onclick="event.stopPropagation();">
                                    <a href="?edit=<?= $customer->user_id ?>&<?= $queryParams ?>" class="btn-edit">Edit</a>
                                    <a href="javascript:void(0)"
                                        onclick="deleteUser('<?= $customer->user_id ?>')"
                                        class="btn-delete">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Table Footer -->
                <div class="table-footer">
                    <p>Showing <?= $startNum ?> to <?= $endNum ?> of <?= $totalCustomers ?> results</p>
                    <div class="pagination">
                        <?php 
                        $baseQueryParams = http_build_query([
                            'search' => $search,
                            'status' => $status,
                            'sort'   => $sortBy,
                            'role'   => $roleFilter,
                        ]);
                        ?>
                        
                        <a href="?page=<?= max(1, $page - 1) ?>&<?= $baseQueryParams ?>" 
                            class="pagination-btn <?= $page <= 1 ? 'disabled' : '' ?>">
                            &lt;
                        </a>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php
                            $editParam = $selectedCustomer ? '&edit=' . $selectedCustomer->user_id : '';
                            ?>
                            <a href="?page=<?= $i ?>&<?= $baseQueryParams ?><?= $editParam ?>"
                                class="pagination-btn <?= $i == $page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <a href="?page=<?= min($totalPages, $page + 1) ?>&<?= $baseQueryParams ?>" 
                            class="pagination-btn <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            &gt;
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL (Edit) -->
        <?php if ($selectedCustomer): ?>
        <div class="edit-panel">
            <h3>Edit Customer</h3>
            <p class="sub">Update the details for <?= htmlspecialchars($selectedCustomer->user_name) ?>.</p>

            <form method="POST" action="?action=update" enctype="multipart/form-data">
                <input type="hidden" name="user_id" value="<?= $selectedCustomer->user_id ?>">

                <div class="profile-upload">
                    <div class="avatar">
                        <?php 
                        $profilePic = getUserProfilePicture($selectedCustomer->user_id);
                        ?>
                        <img src="<?= htmlspecialchars($profilePic) ?>"
                            alt="Profile"
                            style="width:100%;height:100%;object-fit:cover;border-radius:50%;"
                            onerror="this.onerror=null;this.src='<?= $pathPrefix ?>/images/profile/default_profile_picture.webp';">
                        <label for="profile_image" class="avatar-upload-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                <circle cx="12" cy="13" r="4"></circle>
                            </svg>
                        </label>
                        <input type="file" id="profile_image" name="profile_image" style="display:none;" accept="image/*">
                    </div>
                    <p>JPG, GIF, PNG or WEBP. 1MB max.</p>
                </div>

                <label>User ID</label>
                <input type="text"
                    class="form-input readonly"
                    value="<?= htmlspecialchars($selectedCustomer->user_id) ?>"
                    readonly>

                <label>Full Name</label>
                <input type="text"
                    class="form-input"
                    name="user_name"
                    value="<?= htmlspecialchars($selectedCustomer->user_name) ?>"
                    required>

                <label>Email Address</label>
                <input type="email"
                    class="form-input"
                    name="email"
                    value="<?= htmlspecialchars($selectedCustomer->email) ?>"
                    required>

                <label>Phone Number</label>
                <input type="text"
                    class="form-input"
                    name="contact_num"
                    value="<?= htmlspecialchars($selectedCustomer->contact_num ?? '') ?>">

                <label>New Password</label>
                <input type="password"
                    class="form-input"
                    name="password"
                    placeholder="Leave empty to keep current password">

                <label>Gender</label>
                <select name="gender" class="form-select">
                    <option value="">Not Specified</option>
                    <option value="Male" <?= ($selectedCustomer->gender === 'Male') ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= ($selectedCustomer->gender === 'Female') ? 'selected' : '' ?>>Female</option>
                </select>

                <label>Status</label>
                <select name="account_status" class="form-select">
                    <option value="Unblock" <?= $selectedCustomer->account_status === 'Unblock' ? 'selected' : '' ?>>Unblock</option>
                    <option value="Unverify" <?= $selectedCustomer->account_status === 'Unverify' ? 'selected' : '' ?>>Unverified</option>
                    <option value="Blocked" <?= $selectedCustomer->account_status === 'Blocked' ? 'selected' : '' ?>>Blocked</option>
                </select>


                <div class="edit-panel-buttons">
                    <a href="adminCustomer.php?<?= $queryParams ?>" class="cancel">Cancel</a>
                    <button type="submit" class="save">Save Changes</button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Customer Modal -->
<div id="addCustomerModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeAddModal()">&times;</span>
        <h3>Add New Customer</h3>
        
        <form method="POST" action="?action=create" enctype="multipart/form-data">
            <label>User ID <span style="color: #94a3b8; font-size: 12px;">(Optional - Auto-generates M### if empty)</span></label>
            <input type="text" name="custom_user_id" placeholder="Leave empty for auto-generation (or enter any unique ID)">
                   
            <label>User Role</label>
            <select name="user_role" id="modalUserRole" onchange="updateUserIdHint()">
                <option value="Member">Member</option>
                <option value="Admin">Admin</option>
            </select>

            <label>Full Name</label>
            <input type="text" name="user_name" required>

            <label>Email Address</label>
            <input type="email" name="email" required>

            <label>Password (Default: 123456abc)</label>
            <input type="password" name="password" placeholder="Leave empty for default">

            <label>Phone Number</label>
            <input type="text" name="contact_num">

            <label>Gender</label>
            <select name="gender">
                <option value="">Not Specified</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>

            <label>Status</label>
            <select name="account_status">
                <option value="Unblock" selected>Unblock</option>
                <option value="Verified">Verified</option>
                <option value="Unverify">Unverify</option>
            </select>

            <div class="edit-panel-buttons">
                <button type="button" class="cancel" onclick="closeAddModal()">Cancel</button>
                <button type="submit" class="save">Add User</button>
            </div>
        </form>
    </div>
</div>

<script>
// 全选/取消全选
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.user-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateBulkDeleteBtn();
}

// Update the display status of the batch delete button
function updateBulkDeleteBtn() {
    const checkboxes = document.querySelectorAll('.user-checkbox:checked');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectAll = document.getElementById('selectAll');
    const allCheckboxes = document.querySelectorAll('.user-checkbox');
    
    // Show/Hide the batch delete button
    if (checkboxes.length > 0) {
        bulkDeleteBtn.style.display = 'inline-block';
        bulkDeleteBtn.textContent = `🗑 Delete Selected (${checkboxes.length})`;
    } else {
        bulkDeleteBtn.style.display = 'none';
    }
    
    // Update the status of the "Select All" checkbox
    selectAll.checked = allCheckboxes.length > 0 && checkboxes.length === allCheckboxes.length;
}

// Batch Remove
function bulkDelete() {
    const checkboxes = document.querySelectorAll('.user-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('Please select at least one user to delete.');
        return;
    }
    
    if (!confirm(`Are you sure you want to delete ${checkboxes.length} user(s) on this page?`)) {
        return;
    }
    
    // Create a form and submit it
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '?action=bulk_delete';
    
    checkboxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'selected_ids[]';
        input.value = checkbox.value;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}

function updateUserIdHint() {
    const roleSelect = document.getElementById('modalUserRole');
    const prefixHint = document.getElementById('modalPrefixHint');
    if (roleSelect && prefixHint) {
        const selectedRole = roleSelect.value;
        prefixHint.textContent = (selectedRole === 'Admin') ? 'A' : 'M';
    }
}

function showAddModal() {
    const roleSelect = document.getElementById('modalUserRole');
    if (roleSelect) {
        roleSelect.value = 'Member';
        updateUserIdHint();
    }
    document.getElementById('addCustomerModal').style.display = 'flex';
}

function closeAddModal() {
    document.getElementById('addCustomerModal').style.display = 'none';
}

function deleteUser(userId) {
    if (!confirm('Delete this user?')) return;

    fetch('adminCustomerAjax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: userId })
    })
    .then(res => {
        console.log('HTTP status:', res.status);
        return res.text(); // ⚠️ 先别 json
    })
    .then(text => {
        console.log('Response:', text);
        const data = JSON.parse(text);
        if (data.success) {
            document.getElementById('row-' + userId).remove();
        } else {
            alert(data.message);
        }
    })
    .catch(err => {
        console.error('AJAX error:', err);
    });
}


window.onclick = function(event) {
    const modal = document.getElementById('addCustomerModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', updateUserIdHint);
</script>

<?php include 'adminFooter.php'; ?>