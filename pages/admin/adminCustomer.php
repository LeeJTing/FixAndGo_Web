<?php include 'adminHeader.php'; ?>

<div class="customers-page">
    
    <div class="customers-header">
        <div>
            <h1>Customers</h1>
            <p>Manage customer accounts, view purchase history, and handle support.</p>
        </div>
        <button class="add-btn">
            <span>＋</span> Add Customer
        </button>
    </div>

    <div class="customers-content">
        <!-- LEFT SIDE -->
        <div class="left-section">

            <!-- Top: Search + Filters -->
            <div class="customer-toolbar">
                <div class="search-box">
                    <i class="icon-search"></i>
                    <input type="text" placeholder="Search by name, email, or ID...">
                </div>

                <select class="filter-select">
                    <option>Status: All</option>
                    <option>Active</option>
                    <option>Suspended</option>
                    <option>New</option>
                </select>

                <select class="sort-select">
                    <option>Sort by: Date</option>
                    <option>Sort by: Name</option>
                    <option>Sort by: Status</option>
                </select>
            </div>

            <!-- Table -->
            <div class="customer-table-container">
                <table class="customer-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Email</th>
                            <th>Registration Date</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>1</td>
                            <td>Eleanor Vance</td>
                            <td>eleanor.v@example.com</td>
                            <td>2023-05-21</td>
                            <td><span class="status active">Active</span></td>
                        </tr>

                        <tr class="selected">
                            <td><input type="checkbox" checked></td>
                            <td>2</td>
                            <td class="highlight">Marcus Thorne</td>
                            <td>m.thorne@example.com</td>
                            <td>2023-05-20</td>
                            <td><span class="status suspended">Suspended</span></td>
                        </tr>

                        <tr>
                            <td><input type="checkbox"></td>
                            <td>3</td>
                            <td>Isabella Rossi</td>
                            <td>isabella.r@example.com</td>
                            <td>2023-05-19</td>
                            <td><span class="status active">Active</span></td>
                        </tr>

                        <tr>
                            <td><input type="checkbox"></td>
                            <td>4</td>
                            <td>Julian Cross</td>
                            <td>j.cross@example.com</td>
                            <td>2023-05-18</td>
                            <td><span class="status new">New</span></td>
                        </tr>

                    </tbody>
                </table>

                <!-- Table Footer -->
                <div class="table-footer">
                    <p>Showing 1 to 5 of 2,345 results</p>
                    <div class="pagination">
                        <button>&lt;</button>
                        <button>&gt;</button>
                    </div>
                </div>
            </div>
        </div> <!-- end left-section -->


        <!-- RIGHT PANEL -->
        <div class="edit-panel">
            <h3>Edit Customer</h3>
            <p class="sub">Update the details for Marcus Thorne.</p>

            <div class="profile-upload">
                <div class="avatar">
                    <div class="avatar-upload-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                            <circle cx="12" cy="13" r="4"></circle>
                        </svg>
                    </div>
                </div>
                <p>JPG, GIF or PNG. 1MB max.</p>
            </div>

            <label>Full Name</label>
            <input type="text" value="Marcus Thorn">

            <label>Email Address</label>
            <input type="text" value="m.thorne@example.com">

            <label>Phone Number</label>
            <input type="text" value="+1 (555) 123-4567">

            <label>Status</label>
            <select>
                <option>Active</option>
                <option selected>Suspended</option>
            </select>

            <div class="edit-panel-buttons">
                <button class="cancel">Cancel</button>
                <button class="save">Save Changes</button>
            </div>

        </div>
    </div> <!-- end customers-content -->

</div>

<?php include 'adminFooter.php'; ?>
