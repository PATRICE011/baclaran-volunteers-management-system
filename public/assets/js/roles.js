   const storageBaseUrl = "{{ asset('storage') }}";
    @php
    $transformedUsers = $users -> map(function($user) {
        return [
            'id' => $user -> id,
            'first_name' => $user -> first_name,
            'last_name' => $user -> last_name,
            'email' => $user -> email,
            'role' => $user -> role,
            'dateAdded' => $user -> created_at -> format('Y-m-d'),
            'profile_picture' => $user->profile_picture 
        ];
    });
    @endphp

    let users = @json($transformedUsers);



    let filteredUsers = [...users];
    let currentFilter = 'all';
    let currentSort = 'name-asc';

    // DOM Elements
    const searchInput = document.getElementById('search-input');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const sortSelect = document.getElementById('sort-select');
    const rolesTableBody = document.getElementById('roles-tbody');
    const showingCount = document.getElementById('showing-count');
    const totalCount = document.getElementById('total-count');
    const addRoleForm = document.getElementById('add-role-form');
    const toggleFormBtn = document.getElementById('toggle-form');
    const clearFormBtn = document.getElementById('clear-form');
    const deleteModal = document.getElementById('delete-modal');
    const toast = document.getElementById('toast');

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        renderTable();
        updateStats();

        // Set initial active filter
        filterButtons[0].classList.add('filter-active');
    });

    // Search functionality
    searchInput.addEventListener('input', function() {
        applyFilters();
    });

    // Filter buttons
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(b => b.classList.remove('filter-active'));
            // Add active class to clicked button
            this.classList.add('filter-active');

            currentFilter = this.dataset.role;
            applyFilters();
        });
    });

    // Sort functionality
    sortSelect.addEventListener('change', function() {
        currentSort = this.value;
        applyFilters();
    });

    // Form toggle
    toggleFormBtn.addEventListener('click', function() {
        const form = addRoleForm;
        const toggleText = document.getElementById('toggle-text');

        if (form.style.display === 'none') {
            form.style.display = 'block';
            toggleText.textContent = 'Hide Form';
        } else {
            form.style.display = 'none';
            toggleText.textContent = 'Show Form';
        }
    });

    // Clear form
    clearFormBtn.addEventListener('click', function() {
        addRoleForm.reset();
        clearValidationErrors();
    });

    // Form submission
    addRoleForm.addEventListener('submit', function(e) {
        e.preventDefault();

        if (validateForm()) {
            const formData = new FormData(this);

            fetch('{{ route("roles.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        first_name: formData.get('first_name'),
                        last_name: formData.get('last_name'),
                        email: formData.get('email'),
                        password: formData.get('password'),
                        role: formData.get('role')
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || 'Server error');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Add new user to local array
                        const newUser = {
                            id: data.user.id,
                            first_name: data.user.first_name,
                            last_name: data.user.last_name,
                            email: data.user.email,
                            role: data.user.role,
                            dateAdded: data.user.created_at
                        };

                        users.push(newUser);
                        applyFilters();
                        updateStats();
                        showToast('User added successfully!', 'success');
                        this.reset();
                    } else {
                        showToast(data.message || 'An error occurred', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast(error.message || 'An error occurred. Please try again.', 'error');
                });
        }
    });
    // Apply filters and search
    function applyFilters() {
        let filtered = [...users];

        // Apply search filter
        const searchTerm = searchInput.value.toLowerCase();
        if (searchTerm) {
            filtered = filtered.filter(user =>
                (user.first_name + ' ' + user.last_name).toLowerCase().includes(searchTerm) ||
                user.email.toLowerCase().includes(searchTerm)
            );
        }

        // Apply role filter
        if (currentFilter !== 'all') {
            filtered = filtered.filter(user => user.role === currentFilter);
        }

        // Apply sorting
        filtered.sort((a, b) => {
            const nameA = (a.first_name + ' ' + a.last_name).toLowerCase();
            const nameB = (b.first_name + ' ' + b.last_name).toLowerCase();

            switch (currentSort) {
                case 'name-asc':
                    return nameA.localeCompare(nameB);
                case 'name-desc':
                    return nameB.localeCompare(nameA);
                case 'date-asc':
                    return new Date(a.dateAdded) - new Date(b.dateAdded);
                case 'date-desc':
                    return new Date(b.dateAdded) - new Date(a.dateAdded);
                case 'role-asc':
                    return a.role.localeCompare(b.role);
                default:
                    return 0;
            }
        });

        filteredUsers = filtered;
        renderTable();
    }

    // Render table
      function renderTable() {
        if (filteredUsers.length === 0) {
            rolesTableBody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No users found</td></tr>';
            showingCount.textContent = '0';
        } else {
            showingCount.textContent = filteredUsers.length;

            rolesTableBody.innerHTML = filteredUsers.map(user => `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-8 w-8">
                            <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm font-medium overflow-hidden">
                                ${user.profile_picture ? 
                                    `<img src="${storageBaseUrl}/${user.profile_picture}" alt="${user.first_name} ${user.last_name}" class="w-full h-full object-cover">` 
                                    : 
                                    `<img src="https://api.dicebear.com/7.x/avataaars/svg?seed=${encodeURIComponent(user.first_name + ' ' + user.last_name)}" alt="${user.first_name} ${user.last_name}" class="w-full h-full object-cover">`
                                }
                            </div>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900">${user.first_name} ${user.last_name}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${user.email}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="role-badge role-${user.role}">${user.role.charAt(0).toUpperCase() + user.role.slice(1)}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${formatDate(user.dateAdded)}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        <button onclick="editUser(${user.id})" class="text-blue-600 hover:text-blue-900 transition-colors">
                            Edit
                        </button>
                        <button onclick="deleteUser(${user.id}, '${user.first_name} ${user.last_name}')" class="text-red-600 hover:text-red-900 transition-colors">
                            Delete
                        </button>
                    </div>
                </td>
            </tr>
            `).join('');
        }

        totalCount.textContent = users.length;
    }


    // Update statistics
    function updateStats() {
        const totalUsers = users.length;
        const adminCount = users.filter(user => user.role === 'admin').length;

        document.getElementById('total-users').textContent = totalUsers;
        document.getElementById('admin-count').textContent = adminCount;
    }

    // Format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    // Form validation
    function validateForm() {
        clearValidationErrors();
        let isValid = true;

        const firstName = document.getElementById('first_name').value.trim();
        const lastName = document.getElementById('last_name').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        const role = document.getElementById('role').value;

        if (!firstName) {
            showFieldError('first_name', 'First name is required');
            isValid = false;
        }

        if (!lastName) {
            showFieldError('last_name', 'Last name is required');
            isValid = false;
        }

        if (!email) {
            showFieldError('email', 'Email is required');
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showFieldError('email', 'Please enter a valid email address');
            isValid = false;
        } else if (users.some(user => user.email === email)) {
            showFieldError('email', 'Email address already exists');
            isValid = false;
        }

        if (!password) {
        showFieldError('password', 'Password is required');
        isValid = false;
        } else if (password.length < 8) {
            showFieldError('password', 'Password must be at least 8 characters');
            isValid = false;
        } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9])/.test(password)) {
            showFieldError('password', 'Password must contain at least one uppercase letter, one number, and one special character');
            isValid = false;
        }


        if (!confirmPassword) {
            showFieldError('confirm-password', 'Please confirm your password');
            isValid = false;
        } else if (password !== confirmPassword) {
            showFieldError('confirm-password', 'Passwords do not match');
            isValid = false;
        }

        if (!role) {
            showFieldError('role', 'Please select a role');
            isValid = false;
        }

        return isValid;
    }

    function showFieldError(fieldName, message) {
        const errorElement = document.getElementById(fieldName + '-error');
        const inputElement = document.getElementById(fieldName === 'confirm-password' ? 'confirmPassword' : fieldName);

        if (errorElement && inputElement) {
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
            inputElement.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
        }
    }

    function clearValidationErrors() {
        const errorElements = document.querySelectorAll('[id$="-error"]');
        const inputElements = document.querySelectorAll('input, select');

        errorElements.forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });

        inputElements.forEach(el => {
            el.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
        });
    }

    // Delete user functionality
    function deleteUser(userId, userName) {
        document.getElementById('delete-user-name').textContent = userName;
        deleteModal.classList.remove('hidden');
        deleteModal.classList.add('flex');

        // Store the user ID for deletion
        deleteModal.dataset.userId = userId;
    }

    // Modal event listeners
    document.getElementById('cancel-delete').addEventListener('click', function() {
        closeDeleteModal();
    });

    document.getElementById('confirm-delete').addEventListener('click', function() {
        const userId = parseInt(deleteModal.dataset.userId);

        fetch(`/roles/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    users = users.filter(user => user.id !== userId);
                    applyFilters();
                    updateStats();
                    closeDeleteModal();
                    showToast('User deleted successfully!', 'success');
                } else {
                    showToast(data.message || 'Failed to delete user', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred. Please try again.', 'error');
            });
    });

    function closeDeleteModal() {
        deleteModal.classList.add('hidden');
        deleteModal.classList.remove('flex');
    }

    // Edit user (placeholder)
    function editUser(userId) {
        showToast('Edit functionality would be implemented here', 'success');
    }

    // Toast notification
    function showToast(message, type = 'success') {
        toast.textContent = message;
        toast.className = `toast toast-${type} show`;

        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // Close modal when clicking outside
    deleteModal.addEventListener('click', function(e) {
        if (e.target === deleteModal) {
            closeDeleteModal();
        }
    });
    // Toggle password visibility functions
    function togglePasswordVisibility(inputId, toggleButtonId) {
        const input = document.getElementById(inputId);
        const toggleButton = document.getElementById(toggleButtonId);
        const icon = toggleButton.querySelector("svg");

        if (input.type === "password") {
            input.type = "text";
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>`;
        } else {
            input.type = "password";
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88">`;
        }
    }

    // Attach event listeners for password visibility toggles
    document.getElementById("toggleCurrentPassword")?.addEventListener("click", function() {
        togglePasswordVisibility(
            "password",
            "toggleCurrentPassword"
        );
    });
    document.getElementById("toggleConfirmPassword")?.addEventListener("click", function() {
        togglePasswordVisibility(
            "confirmPassword",
            "toggleConfirmPassword"
        );
    });