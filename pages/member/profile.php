<?php
require "../../_base.php";
$_title = 'Fix & GO | Profile';
include "../../_head.php";
require "../../dao/profile_dao.php";

$user_id = temp('USER_ID');
$user_name = temp('USER_NAME');
$user_role = temp('USER_ROLE');
$user_status = temp('ACCOUNT_STATUS');
$user_email = temp('EMAIL');
$user_profile = getUserProfileDetails($user_id);
$user_devices = getUserDevices($user_id);
$addresses = getAddressesByUserId($user_id);
?>

<link rel="stylesheet" href="<?= $rootDir ?>/css/profile.css">
<main>
    <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-picture-section">
                <div class="profile-picture-container">
                    <img src="<?= getUserProfilePicture($user_id) ?>"
                        alt="Profile Picture" class="profile-picture1">
                    <div class="profile-status online"></div>
                </div>
                <div class="profile-info">
                    <h1 class="profile-name"><?= htmlspecialchars($user_name) ?></h1>
                    <p class="profile-role"><?= htmlspecialchars($user_role) ?></p>
                    <p class="profile-email"><?= htmlspecialchars($user_email) ?></p>
                    <button class="edit-profile-btn">
                        <i class="fas fa-edit"></i> Edit Profile
                    </button>
                </div>
            </div>
        </div>

        <!-- Profile Content -->
        <div class="profile-content">
            <!-- Left Column - Personal Information -->
            <div class="left-column">
                <div class="info-card">
                    <div class="card-header">
                        <h2><i class="fas fa-user-circle"></i> Personal Information</h2>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-id-card"></i>
                                User ID
                            </div>
                            <div class="info-value"><?= htmlspecialchars($user_id) ?></div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-user"></i>
                                Full Name
                            </div>
                            <div class="info-value"><?= htmlspecialchars($user_name) ?></div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-birthday-cake"></i>
                                Date of Birth
                            </div>
                            <div class="info-value"><?= $user_profile->dob ?? '<i>None</i>' ?>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-venus-mars"></i>
                                Gender
                            </div>
                            <div class="info-value"><?= $user_profile->gender ?? '<i>None</i>' ?></div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-shield-alt"></i>
                                User Role
                            </div>
                            <div class="info-value">
                                <span class="role-badge customer"><?= htmlspecialchars($user_role) ?></span>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-envelope"></i>
                                Email Address
                            </div>
                            <div class="info-value"><?= htmlspecialchars($user_email) ?></div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-phone"></i>
                                Contact Number
                            </div>
                            <div class="info-value"><?= $user_profile->contact_num ?? '<i>None</i>' ?></div>
                        </div>

                        <!-- <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-check-circle"></i>
                                Email Verified
                            </div>
                            <div class="info-value">
                                <span class="verification-status yes">Yes</span>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-phone-alt"></i>
                                Phone Verified
                            </div>
                            <div class="info-value">
                                <span class="verification-status yes">Yes</span>
                            </div>
                        </div> -->
                    </div>
                </div>

                <div class="info-card">
                    <div class="card-header">
                        <h2><i class="fas fa-chart-line"></i> Account Stats</h2>
                    </div>
                    <div class="card-body">
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-number"><?= countOrdersById($user_id) ?></div>
                                <div class="stat-label">Orders</div>
                            </div>
                            <!-- <div class="stat-item">
                                <div class="stat-number">8</div>
                                <div class="stat-label">Wishlist</div>
                            </div> -->
                            <div class="stat-item">
                                <div class="stat-number"><?= countReviewById($user_id) ?></div>
                                <div class="stat-label">Reviews</div>
                            </div>
                            <!-- <div class="stat-item">
                                <div class="stat-number">$2,450</div>
                                <div class="stat-label">Total Spent</div>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Addresses & Actions -->
            <div class="right-column">
                <div class="info-card">
                    <div class="card-header">
                        <h2><i class="fas fa-laptop"></i> My Devices</h2>
                    </div>
                    <div class="card-body">
                        <?php foreach($user_devices as $key => $value): ?>
                        <div class="device-card">
                            <div class="device-info">
                                <div class="device-details">
                                    <h3><?= $value->device_name ?></h3>
                                </div>
                            </div>
                            <div class="device-meta">
                                <button class="device-action remove">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                        <?php endforeach ?>
                    </div>
                </div>
                <div class="info-card">
                    <div class="card-header">
                        <h2><i class="fas fa-map-marker-alt"></i> My Addresses</h2>
                        <button class="add-address-btn">
                            <i class="fas fa-plus"></i> Add New
                        </button>
                    </div>
                    <div class="card-body">
                        <?php foreach($addresses as $key => $value) : ?>
                        <div class="address-card">
                            <div class="address-header">
                                <h3><?= $value->address_name ?></h3>
                            </div>
                            <div class="address-content">
                                <p><?= $value->address_one ? $value->address_one . ',' : '<i>None</i>'?></p>
                                <p><?= $value->address_two ? $value->address_two . ',' : '' ?></p>
                                <p><?= $value->address_three ? $value->address_three . ', ' : '' ?><?= $value->post_code . ','?></p>
                                <p><?= $value->state ?>, <?= $value->country ?></p>
                            </div>
                            <div class="address-actions">
                                <button class="action-btn edit">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="action-btn delete">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                        <?php endforeach ?>
                    </div>
                </div>

                <div class="info-card">
                    <div class="card-header">
                        <h2><i class="fas fa-cog"></i> Account Actions</h2>
                    </div>
                    <div class="card-body">
                        <div class="action-grid">
                            <button class="action-card">
                                <i class="fas fa-lock"></i>
                                <span>Change Password</span>
                            </button>
                            <!-- <button class="action-card">
                                <i class="fas fa-bell"></i>
                                <span>Notifications</span>
                            </button> -->
                            <!-- <button class="action-card">
                                <i class="fas fa-credit-card"></i>
                                <span>Payment Methods</span>
                            </button> -->
                            <button class="action-card">
                                <i class="fas fa-history"></i>
                                <span>Order History</span>
                            </button>
                            <!-- <button class="action-card">
                                <i class="fas fa-heart"></i>
                                <span>Wishlist</span>
                            </button> -->
                            <button class="action-card danger">
                                <i class="fas fa-user-slash"></i>
                                <span>Delete Account</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Footer -->
        <!-- <div class="profile-footer">
            <p>Last Updated: Today, 10:30 AM</p>
            <div class="footer-actions">
                <button class="secondary-btn">
                    <i class="fas fa-download"></i> Export Data
                </button>
                <button class="secondary-btn">
                    <i class="fas fa-print"></i> Print Profile
                </button>
            </div>
        </div> -->
    </div>
</main>
<?php
include "../../_foot.php";
