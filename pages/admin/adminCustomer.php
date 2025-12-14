<?php
// pages/admin/adminCustomer.php
require_once __DIR__ . '/../../_base.php';
require_once __DIR__ . '/../../controller/customer-controller.php';

// 处理操作
$action = get('action');

if ($action == 'delete' && get('id')) {
    CustomerController::handleDelete(get('id'));
}

if ($action == 'update' && is_post()) {
    CustomerController::handleUpdate();
}

if ($action == 'create' && is_post()) {
    CustomerController::handleCreate();
}

// 获取搜索和筛选参数
$search = get('search');
$status = get('status', 'All');
$sortBy = get('sort', 'user_id');

$roleFilter = get('role', 'Member');

$queryParams = http_build_query([
    'search' => $search,
    'status' => $status,
    'sort'   => $sortBy,
    'role'   => $roleFilter
]);

// 获取用户列表（只显示 Members）
$customers = CustomerDAO::getAllCustomers($search, $status, $sortBy, $roleFilter);
$totalCustomers = CustomerDAO::getTotalCount($search, $status, $roleFilter);

// 获取选中的用户（用于编辑面板）
$selectedCustomer = null;
if (get('edit')) {
    $selectedCustomer = CustomerDAO::getCustomerById(get('edit'));
}

// 获取提示信息
$successMsg = temp('success');
$errorMsg = temp('error');

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
        </div>
    </div>

    <div class="customers-content">
        <!-- LEFT SIDE -->
        <div class="left-section">

            <!-- Top: Search + Filters -->
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
                    <option value="Verified" <?= $status == 'Verified' ? 'selected' : '' ?>>Verified</option>
                    <option value="Unverified" <?= $status == 'Unverified' ? 'selected' : '' ?>>Unverified</option>
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
                                <td colspan="6" style="text-align: center; padding: 30px;">
                                    No customers found
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($customers as $customer): ?>
                            <tr class="<?= ($selectedCustomer && $selectedCustomer->user_id == $customer->user_id) ? 'selected' : '' ?>" 
                                onclick="window.location.href='?edit=<?= $customer->user_id ?>&<?= $queryParams ?>'"
                                style="cursor: pointer;">
                                <td><?= htmlspecialchars($customer->user_id) ?></td>
                                <td class="<?= ($selectedCustomer && $selectedCustomer->user_id == $customer->user_id) ? 'highlight' : '' ?>">
                                    <?= htmlspecialchars($customer->user_name) ?>
                                </td>
                                <td><?= htmlspecialchars($customer->email) ?></td>
                                <td><?= htmlspecialchars($customer->contact_num ?? 'N/A') ?></td>
                                <td>
                                    <?php
                                    $statusClass = strtolower($customer->account_status);
                                    if ($statusClass == 'verified') $statusClass = 'active';
                                    if ($statusClass == 'blocked') $statusClass = 'suspended';
                                    if ($statusClass == 'unverified') $statusClass = 'new';
                                    ?>
                                    <span class="status <?= $statusClass ?>">
                                        <?= htmlspecialchars($customer->account_status) ?>
                                    </span>
                                </td>
                                <td onclick="event.stopPropagation();">
                                    <a href="?edit=<?= $customer->user_id ?>&<?= $queryParams ?>" class="btn-edit">Edit</a>
                                    <a href="?action=delete&id=<?= $customer->user_id ?>&<?= $queryParams ?>"
                                    onclick="return confirm('Are you sure you want to delete this customer?')" 
                                    class="btn-delete">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Table Footer -->
                <div class="table-footer">
                    <p>Showing <?= count($customers) ?> of <?= $totalCustomers ?> results</p>
                    <div class="pagination">
                        <button>&lt;</button>
                        <button>&gt;</button>
                    </div>
                </div>
            </div>
        </div> <!-- end left-section -->


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
                        // 确保路径正确
                        if (empty($profilePic) || $profilePic == '/') {
                            $profilePic = '/images/profile/default_profile_picture.webp';
                        }
                        ?>
                        <img src="../../<?= htmlspecialchars($profilePic) ?>"
                        alt="Profile" 
                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;"
                        onerror="this.onerror=null; this.src='../../images/profile/default_profile_picture.webp';">
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

                <!-- User ID - Read Only -->
                <label>User ID</label>
                <input type="text" value="<?= htmlspecialchars($selectedCustomer->user_id) ?>" readonly 
                    style="background: #0f172a; color: #94a3b8; cursor: not-allowed;">

                <label>Full Name</label>
                <input type="text" name="user_name" value="<?= htmlspecialchars($selectedCustomer->user_name) ?>" required>

                <label>Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($selectedCustomer->email) ?>" required>

                <label>Phone Number</label>
                <input type="text" name="contact_num" value="<?= htmlspecialchars($selectedCustomer->contact_num ?? '') ?>">
                    
                <label>New Password</label>
                <input type="password" name="password" placeholder="Leave empty to keep current password">

                <label>Gender</label>
                <select name="gender">
                    <option value="">Not Specified</option>
                    <option value="Male" <?= ($selectedCustomer->gender == 'Male') ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= ($selectedCustomer->gender == 'Female') ? 'selected' : '' ?>>Female</option>
                </select>

                <label>Status</label>
                <select name="account_status">
                    <option value="Verified" <?= $selectedCustomer->account_status == 'Verified' ? 'selected' : '' ?>>Verified</option>
                    <option value="Unverified" <?= $selectedCustomer->account_status == 'Unverified' ? 'selected' : '' ?>>Unverified</option>
                    <option value="Blocked" <?= $selectedCustomer->account_status == 'Blocked' ? 'selected' : '' ?>>Blocked</option>
                </select>

                <div class="edit-panel-buttons">
                    <a href="adminCustomer.php?<?= $queryParams ?>" class="cancel">Cancel</a>
                    <button type="submit" class="save">Save Changes</button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div> <!-- end customers-content -->

</div>

<!-- Add Customer Modal -->
<div id="addCustomerModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeAddModal()">&times;</span>
        <h3>Add New Customer</h3>
        
        <form method="POST" action="?action=create" enctype="multipart/form-data">

        <!-- User ID Input - Optional, will auto-generate if empty -->
            <label>User ID <span style="color: #94a3b8; font-size: 12px;">(Optional - Auto-generates M### if empty)</span></label>
            <input type="text" name="custom_user_id" placeholder="e.g., M100 or leave empty for auto-generation" 
                   pattern="[MA][0-9]{3,}" title="Must start with M or A followed by numbers">
                   
            <label>User Role</label>
            <select name="user_role" id="modalUserRole" onchange="updateUserIdHint()">
                <option value="Member">Member</option>
                <option value="Admin">Admin</option>
            </select>

            <label>Full Name</label>
            <input type="text" name="user_name" required>

            <label>Email Address</label>
            <input type="email" name="email" required>

            <label>Password (Default: password123)</label>
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
                <option value="Verified">Verified</option>
                <option value="Unverified">Unverified</option>
                <option value="Blocked">Blocked</option>
            </select>

            <div class="edit-panel-buttons">
                <button type="button" class="cancel" onclick="closeAddModal()">Cancel</button>
                <button type="submit" class="save">Add Customer</button>
            </div>
        </form>
    </div>
</div>

<script>
function updateUserIdHint() {
    const roleSelect = document.getElementById('modalUserRole');
    const prefixHint = document.getElementById('modalPrefixHint');
    if (roleSelect && prefixHint) {
        const selectedRole = roleSelect.value;
        prefixHint.textContent = (selectedRole === 'Admin') ? 'A' : 'M';
    }
}

function showAddModal() {
    // 确保每次打开时角色选择器和提示都重置为 Member (默认)
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

window.onclick = function(event) {
    const modal = document.getElementById('addCustomerModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
document.addEventListener('DOMContentLoaded', updateUserIdHint);
</script>

<?php include 'adminFooter.php'; ?>