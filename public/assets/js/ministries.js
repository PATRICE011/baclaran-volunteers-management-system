document.addEventListener('DOMContentLoaded', function() {
    Alpine.data('ministries', () => ({
        showModal: false,
        showAddModal: false,
        showEditModal: false,
        selectedMinistry: null,
        selectedVolunteers: [],
        selectedCategory: 'All',
        searchQuery: '',
        ministries: [], // Initially empty, will be populated from Blade
        parentMinistries: [],
        loading: false,  // Ensure this is initialized here

        formData: {
            ministry_name: '',
            ministry_code: '',
            ministry_type: '',
            parent_id: null
        },

        ministryTypes: [
            { value: 'LITURGICAL', label: 'Liturgical' },
            { value: 'PASTORAL', label: 'Pastoral' },
            { value: 'SOCIAL_MISSION', label: 'Social Mission' },
            { value: 'SUB_GROUP', label: 'Sub Group' }
        ],

        // Computed property to filter ministries based on category and search
        get filteredMinistries() {
            return this.ministries.filter(ministry => {
                const matchesCategory = this.selectedCategory === 'All' || ministry.category === this.selectedCategory;
                const matchesSearch = this.searchQuery === '' || 
                    ministry.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    ministry.code?.toLowerCase().includes(this.searchQuery.toLowerCase());
                return matchesCategory && matchesSearch;
            });
        },

        get categories() {
            const cats = [...new Set(this.ministries.map(m => m.category))];
            return cats.sort();
        },

        init() {
            // Initialize ministries data from Blade
            this.ministries = JSON.parse(document.getElementById('ministries-data').textContent);
            this.loadParentMinistries();
        },

        // Fetch parent ministries
        async loadParentMinistries() {
            try {
                const response = await fetch('/ministries/parents/list');
                const data = await response.json();
                if (data.success) {
                    this.parentMinistries = data.parents;
                }
            } catch (error) {
                console.error('Error loading parent ministries:', error);
            }
        },

        // Fetch details of a ministry
        async viewMinistry(ministry) {
            this.loading = true;  // Set loading to true when starting to load
            try {
                const response = await fetch(`/ministries/${ministry.id}`);
                const data = await response.json();
                if (data.success) {
                    this.selectedMinistry = data.ministry;
                    this.selectedVolunteers = data.volunteers || [];
                    this.showModal = true;
                }
            } catch (error) {
                console.error('Error loading ministry details:', error);
                alert('Error loading ministry details');
            } finally {
                this.loading = false;  // Reset loading when done
            }
        },

        openAddModal() {
            this.resetForm();
            this.showAddModal = true;
        },

        openEditModal(ministry) {
            this.formData = {
                ministry_name: ministry.name,
                ministry_code: ministry.code || '',
                ministry_type: ministry.type,
                parent_id: ministry.parent_id
            };
            this.selectedMinistry = ministry;
            this.showEditModal = true;
        },

        resetForm() {
            this.formData = {
                ministry_name: '',
                ministry_code: '',
                ministry_type: '',
                parent_id: null
            };
        },

        // Save the new ministry
        async saveMinistry() {
            this.loading = true;  // Set loading state to true while saving
            try {
                const response = await fetch('/ministries', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.formData)
                });

                const data = await response.json();
                if (data.success) {
                    this.ministries.push(data.ministry);
                    this.showAddModal = false;
                    this.resetForm();
                    alert('Ministry created successfully');
                } else {
                    this.handleErrors(data.errors);
                }
            } catch (error) {
                console.error('Error creating ministry:', error);
                alert('Error creating ministry');
            } finally {
                this.loading = false;  // Reset loading when done
            }
        },

        // Update existing ministry
        async updateMinistry() {
            this.loading = true;
            try {
                const response = await fetch(`/ministries/${this.selectedMinistry.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.formData)
                });

                const data = await response.json();
                if (data.success) {
                    const index = this.ministries.findIndex(m => m.id === this.selectedMinistry.id);
                    if (index !== -1) {
                        this.ministries[index] = data.ministry;
                    }
                    this.showEditModal = false;
                    this.resetForm();
                    alert('Ministry updated successfully');
                } else {
                    this.handleErrors(data.errors);
                }
            } catch (error) {
                console.error('Error updating ministry:', error);
                alert('Error updating ministry');
            } finally {
                this.loading = false;
            }
        },

        // Delete a ministry
        async deleteMinistry(ministry) {
            if (!confirm(`Are you sure you want to delete ${ministry.name}?`)) {
                return;
            }

            this.loading = true;
            try {
                const response = await fetch(`/ministries/${ministry.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();
                if (data.success) {
                    this.ministries = this.ministries.filter(m => m.id !== ministry.id);
                    this.showModal = false;
                    alert('Ministry deleted successfully');
                } else {
                    alert(data.message || 'Error deleting ministry');
                }
            } catch (error) {
                console.error('Error deleting ministry:', error);
                alert('Error deleting ministry');
            } finally {
                this.loading = false;
            }
        },

        // Handle validation errors
        handleErrors(errors) {
            if (errors) {
                const errorMessages = Object.values(errors).flat().join('\n');
                alert(errorMessages);
            }
        }
    }));
});
