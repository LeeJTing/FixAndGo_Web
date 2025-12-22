<?php
require_once __DIR__ . "/../dao/profile_dao.php";
require_once __DIR__ . "/../controller/profile-controller.php";

$user_id = temp('USER_ID');
$user_name = temp('USER_NAME');
$user_role = temp('USER_ROLE');
$user_status = temp('ACCOUNT_STATUS');
$user_email = temp('EMAIL');
$user_profile = getUserProfileDetails($user_id);
$addresses = getAddressesByUserId($user_id);
?>

<link rel="stylesheet" href="<?= $rootDir ?>/css/profile.css">
<script src="<?= $rootDir ?>/js/validation.js"></script>
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
                                Date of Birth (year-month-date)
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
                        <h2><i class="fas fa-map-marker-alt"></i> My Addresses</h2>
                        <?php if (getAddressCountByUserId($user_id) < 3) : ?>
                            <button class="add-address-btn">
                                <i class="fas fa-plus"></i> Add New
                            </button>
                        <?php endif ?>
                    </div>
                    <div class="card-body">
                        <?php if ($addresses) :
                            foreach ($addresses as $key => $value) : ?>
                                <div class="address-card">
                                    <div class="address-header">
                                        <h3><?= $value->address_name ?></h3>
                                    </div>
                                    <div class="address-content">
                                        <p><?= $value->address_one ? $value->address_one . ',' : '<i>None</i>' ?></p>
                                        <p><?= $value->address_two ? $value->address_two . ',' : '' ?></p>
                                        <p><?= $value->address_three ? $value->address_three . ', ' : '' ?><?= $value->post_code . ',' ?></p>
                                        <p><?= $value->state ?>, <?= $value->country ?></p>
                                    </div>
                                    <div class="address-actions">
                                        <button class="action-btn edit edit-address-btn" value="updateAddress"
                                            data-address-id="<?= $value->address_id ?>"
                                            data-address-name="<?= htmlspecialchars($value->address_name) ?>"
                                            data-address-one="<?= htmlspecialchars($value->address_one) ?>"
                                            data-address-two="<?= $value->address_two ? htmlspecialchars($value->address_two) : NULL ?>"
                                            data-address-three="<?= $value->address_three ? htmlspecialchars($value->address_three) : NULL ?>"
                                            data-post-code="<?= htmlspecialchars($value->post_code) ?>"
                                            data-state="<?= htmlspecialchars($value->state) ?>"
                                            data-country="<?= htmlspecialchars($value->country) ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="action-btn delete delete-address-btn" data-address-id="<?= $value->address_id ?>">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach;
                        else : ?>
                            <p style="color: rgba(135, 135, 135, 1);"><i>No address exist</i></p>
                        <?php endif ?>
                    </div>
                </div>

                <div class="info-card">
                    <div class="card-header">
                        <h2><i class="fas fa-cog"></i> Account Actions</h2>
                    </div>
                    <div class="card-body">
                        <div class="action-grid">
                            <button id="openChangePassword" class="action-card">
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
                            <a href="<?= $rootDir ?>/pages/order/member-order-history.php" class="action-card" style="text-decoration: none;">
                                <i class="fas fa-history"></i>
                                <span>Order History</span>
                            </a>
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

    <!-- Edit Profile Modal -->
    <div id="editProfileModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-edit"></i> Edit Profile</h2>
                <button class="close-btn">x</button>
            </div>
            <form id="editProfileForm" class="modal-form" method="POST" action="?update=profile" enctype="multipart/form-data">
                <!-- Profile Picture Upload -->
                <div class="form-group">
                    <label>Profile Picture</label>
                    <div class="profile-picture-upload">
                        <div class="current-picture">
                            <img src="<?= getUserProfilePicture($user_id) ?>"
                                alt="Current Picture" id="currentProfilePicture" class="modal-profile-picture">
                        </div>
                        <div class="upload-options">
                            <button type="button" class="upload-btn" id="uploadPictureBtn">
                                <i class="fas fa-camera"></i> Upload New
                            </button>
                            <input type="file" id="profileImage" name="profile_image" accept="image/*" style="display: none;">
                        </div>
                        <div class="reset-options">
                            <button type="button" class="reset-btn" id="resetPictureBtn">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name <span style="color: red;">*</span></label>
                        <input type="text" name="user_name" id="editUserName"
                            class="form-input" value="<?= htmlspecialchars($user_name) ?>" required>
                        <div class="error-message" id="nameError"></div>
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" id="editDob"
                            class="form-input" value="<?= !empty($user_profile->dob) ? $user_profile->dob : '' ?>">
                        <div class="error-message" id="dobError"></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender" id="editGender" class="form-input">
                            <option value="" <?= empty($user_profile->gender) ? 'selected' : '' ?>>--Select Genter--</option>
                            <option value="Male" <?= ($user_profile->gender ?? '') == 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= ($user_profile->gender ?? '') == 'Female' ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="tel" name="contact_number" id="editContactNumber"
                            class="form-input" value="<?= htmlspecialchars($user_profile->contact_num ?? '') ?>"
                            placeholder="+60123456789">
                        <div class="error-message" id="phoneError"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" id="editEmail"
                        class="form-input" disabled value="<?= htmlspecialchars($user_email) ?>" required>
                    <div class="error-message" id="emailError"></div>
                </div>

                <div class="form-actions">
                    <button type="button" class="cancel-btn">
                        Cancel
                    </button>
                    <button type="submit" class="save-btn" id="saveProfileBtn">
                        <span class="btn-text">Save Changes</span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Add Address Modal -->
    <div id="addAddressModal" class="address-modal-overlay">
        <div class="address-modal-container">
            <div class="address-modal-header">
                <h2 class="address-modal-title">
                    <i class="fas fa-map-marker-alt"></i>
                    Add New Address
                </h2>
                <button class="address-modal-close" id="closeAddressModal">&times;</button>
            </div>

            <div class="address-modal-body">
                <form method="POST" id="addAddressForm" action="?update=address">
                    <input type="hidden" id="addressId" name="address_id" value="">
                    <!-- Address Name -->
                    <div class="address-form-group">
                        <label for="addressName" class="address-form-label required">Address Name</label>
                        <input type="text"
                            id="addressName"
                            name="address_name"
                            class="address-form-input"
                            placeholder="e.g., Home, Office, Apartment"
                            maxlength="50"
                            required>
                        <div class="address-form-error" id="addressNameError"></div>
                    </div>

                    <!-- Address Line 1 -->
                    <div class="address-form-group">
                        <label for="addressLine1" class="address-form-label required">Address Line 1</label>
                        <input type="text"
                            id="addressLine1"
                            name="address_one"
                            class="address-form-input"
                            placeholder="House number, street name"
                            maxlength="100"
                            required>
                        <div class="address-form-error" id="addressLine1Error"></div>
                    </div>

                    <!-- Address Line 2 -->
                    <div class="address-form-group">
                        <label for="addressLine2" class="address-form-label">Address Line 2 (Optional)</label>
                        <input type="text"
                            id="addressLine2"
                            name="address_two"
                            class="address-form-input"
                            placeholder="Apartment, suite, unit, building, floor"
                            maxlength="100">
                    </div>

                    <!-- Address Line 3 -->
                    <div class="address-form-group">
                        <label for="addressLine3" class="address-form-label">Address Line 3 (Optional)</label>
                        <input type="text"
                            id="addressLine3"
                            name="address_three"
                            class="address-form-input"
                            placeholder="Additional address information"
                            maxlength="100">
                    </div>

                    <!-- Post Code and State -->
                    <div class="address-form-row">
                        <div class="address-form-group">
                            <label for="postCode" class="address-form-label required">Post Code</label>
                            <input type="text"
                                id="postCode"
                                name="post_code"
                                class="address-form-input"
                                placeholder="e.g., 50000"
                                maxlength="10"
                                pattern="[0-9]*"
                                inputmode="numeric"
                                required>
                            <div class="address-form-error" id="postCodeError"></div>
                        </div>

                        <div class="address-form-group">
                            <label for="state" class="address-form-label required">State</label>
                            <select id="state" name="state" class="address-form-input" required>
                                <option value="">Select State</option>
                                <option value="Johor">Johor</option>
                                <option value="Kedah">Kedah</option>
                                <option value="Kelantan">Kelantan</option>
                                <option value="Malacca">Malacca</option>
                                <option value="Negeri Sembilan">Negeri Sembilan</option>
                                <option value="Pahang">Pahang</option>
                                <option value="Penang">Penang</option>
                                <option value="Perak">Perak</option>
                                <option value="Perlis">Perlis</option>
                                <option value="Sabah">Sabah</option>
                                <option value="Sarawak">Sarawak</option>
                                <option value="Selangor">Selangor</option>
                                <option value="Terengganu">Terengganu</option>
                                <option value="Kuala Lumpur">Kuala Lumpur</option>
                                <option value="Labuan">Labuan</option>
                                <option value="Putrajaya">Putrajaya</option>
                            </select>
                            <div class="address-form-error" id="stateError"></div>
                        </div>
                    </div>

                    <!-- Country -->
                    <div class="address-form-group">
                        <label for="country" class="address-form-label required">Country</label>
                        <select id="country" name="country" class="address-form-input" required>
                            <option value="">Select Country</option>
                            <option value="Malaysia" selected>Malaysia</option>
                            <option value="Singapore">Singapore</option>
                            <option value="Thailand">Thailand</option>
                            <option value="Indonesia">Indonesia</option>
                            <option value="Brunei">Brunei</option>
                            <option value="Philippines">Philippines</option>
                            <option value="Vietnam">Vietnam</option>
                            <option value="Other">Other</option>
                        </select>
                        <div class="address-form-error" id="countryError"></div>
                    </div>
                </form>

                <!-- Loading Indicator -->
                <div class="address-modal-loading" id="addressLoading">
                    <div class="spinner"></div>
                    <p>Saving address...</p>
                </div>
            </div>

            <div class="address-form-footer">
                <button type="button" class="cancel-btn" id="cancelAddressBtn">
                    Cancel
                </button>
                <button type="submit" class="save-btn" id="saveAddressBtn" form="addAddressForm">
                    Save Address
                </button>
            </div>
        </div>
    </div>
    <!-- Change Password Modal -->
    <div id="changePasswordModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-lock"></i> Change Password</h2>
                <button class="close-btn">x</button>
            </div>

            <form method="POST" action="?update=password" class="modal-form" id="changePasswordForm">

                <div class="form-group">
                    <label>Old Password <span style="color:red">*</span></label>
                    <input type="password"
                        id="old_password"
                        name="old_password"
                        class="form-input"
                        required>
                    <div class="error-message" id="oldPasswordError"></div>
                </div>

                <div class="form-group">
                    <label>New Password <span style="color:red">*</span></label>
                    <input type="password"
                        id="new_password"
                        name="new_password"
                        class="form-input"
                        minlength="8"
                        required>
                    <div class="error-message" id="newPasswordError"></div>
                    <!-- Password must be 8-12 characters with at least one letter and one number. -->
                    <div class="password-strength">
                        <div class="password-strength-bar" id="passwordStrengthBar"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Confirm Password <span style="color:red">*</span></label>
                    <input type="password"
                        id="confirm_password"
                        name="confirm_password"
                        class="form-input"
                        required>
                    <div class="correct-message" id="confirmPasswordCorrect"></div>
                    <div class="error-message" id="confirmPasswordError"></div>
                </div>

                <div class="form-actions">
                    <button type="button" class="cancel-btn">
                        Cancel
                    </button>
                    <button type="submit" class="save-btn" id='changePasswordBtn'>
                        Update Password
                    </button>
                </div>

            </form>
        </div>
    </div>

</main>
<script src="<?= $rootDir ?>/js/profile.js"></script>
<script>
    // Force the Customer page to use the light mode
    document.body.setAttribute('data-theme', 'light');
</script>