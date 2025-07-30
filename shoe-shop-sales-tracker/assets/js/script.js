document.addEventListener("DOMContentLoaded", () => {
    // Search functionality with debounce
    const searchInput = document.getElementById("searchInput");
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener("input", function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll(".data-table tbody tr");
                
                rows.forEach(row => {
                    const textContent = row.textContent.toLowerCase();
                    row.style.display = textContent.includes(filter) ? "" : "none";
                    
                    // Highlight matching text
                    if (filter && textContent.includes(filter)) {
                        highlightText(row, filter);
                    } else {
                        removeHighlights(row);
                    }
                });
            }, 300);
        });
    }
    
    // Form field enhancements
    const formFields = document.querySelectorAll("input, select, textarea");
    formFields.forEach(field => {
        // Add focus styles
        field.addEventListener("focus", () => {
            field.parentElement.classList.add("focused");
        });
        
        field.addEventListener("blur", () => {
            field.parentElement.classList.remove("focused");
        });
        
        // Add validation classes
        field.addEventListener("input", () => {
            if (field.checkValidity()) {
                field.classList.add("valid");
                field.classList.remove("invalid");
            } else {
                field.classList.add("invalid");
                field.classList.remove("valid");
            }
        });
    });
    
    // Floating label functionality
    const floatingInputs = document.querySelectorAll(".floating-input");
    floatingInputs.forEach(input => {
        input.addEventListener("focus", () => {
            const label = input.nextElementSibling;
            label.classList.add("active");
        });
        
        input.addEventListener("blur", () => {
            if (!input.value) {
                const label = input.nextElementSibling;
                label.classList.remove("active");
            }
        });
        
        // Initialize labels if input has value
        if (input.value) {
            const label = input.nextElementSibling;
            label.classList.add("active");
        }
    });
    
    // Toast notifications
    const toastMessages = document.querySelectorAll(".alert");
    toastMessages.forEach(toast => {
        setTimeout(() => {
            toast.style.opacity = "0";
            setTimeout(() => toast.remove(), 500);
        }, 5000);
    });
    
    // Confirm before destructive actions
    const deleteButtons = document.querySelectorAll(".btn-danger, [data-confirm]");
    deleteButtons.forEach(button => {
        button.addEventListener("click", (e) => {
            const message = button.dataset.confirm || "Are you sure you want to perform this action?";
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
    
    // Toggle password visibility
    const passwordToggles = document.querySelectorAll(".toggle-password");
    passwordToggles.forEach(toggle => {
        toggle.addEventListener("click", function() {
            const input = this.previousElementSibling;
            const type = input.getAttribute("type") === "password" ? "text" : "password";
            input.setAttribute("type", type);
            this.classList.toggle("fa-eye");
            this.classList.toggle("fa-eye-slash");
        });
    });
    
    // Helper functions
    function highlightText(element, text) {
        removeHighlights(element);
        const regex = new RegExp(text, "gi");
        const nodes = element.childNodes;
        
        nodes.forEach(node => {
            if (node.nodeType === Node.TEXT_NODE) {
                const span = document.createElement("span");
                span.innerHTML = node.textContent.replace(regex, match => 
                    `<span class="highlight">${match}</span>`
                );
                node.replaceWith(span);
            } else if (node.nodeType === Node.ELEMENT_NODE && !node.classList.contains("highlight")) {
                highlightText(node, text);
            }
        });
    }
    
    function removeHighlights(element) {
        const highlights = element.querySelectorAll(".highlight");
        highlights.forEach(highlight => {
            const parent = highlight.parentNode;
            parent.replaceWith(highlight.textContent);
        });
    }
    
    // Initialize tooltips
    const tooltips = document.querySelectorAll("[data-tooltip]");
    tooltips.forEach(el => {
        el.addEventListener("mouseenter", showTooltip);
        el.addEventListener("mouseleave", hideTooltip);
    });
    
    function showTooltip(e) {
        const tooltipText = this.getAttribute("data-tooltip");
        const tooltip = document.createElement("div");
        tooltip.className = "tooltip";
        tooltip.textContent = tooltipText;
        document.body.appendChild(tooltip);
        
        const rect = this.getBoundingClientRect();
        tooltip.style.left = `${rect.left + rect.width / 2 - tooltip.offsetWidth / 2}px`;
        tooltip.style.top = `${rect.top - tooltip.offsetHeight - 5}px`;
        
        this.tooltip = tooltip;
    }
    
    function hideTooltip() {
        if (this.tooltip) {
            this.tooltip.remove();
            this.tooltip = null;
        }
    }
});

// AJAX helper function
async function fetchData(url, options = {}) {
    try {
        const response = await fetch(url, {
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            ...options
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        showToast('An error occurred. Please try again.', 'error');
        return null;
    }
}

// Toast notification function
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 500);
    }, 5000);
}

// Add to global scope for easy access
window.helpers = {
    fetchData,
    showToast
};
// Add to existing script.js

// Sales form functionality
function setupSalesForm() {
    const saleForm = document.getElementById('saleForm');
    if (!saleForm) return;

    // Add new item row
    document.getElementById('addItem').addEventListener('click', function() {
        const itemCount = document.querySelectorAll('.sale-item').length;
        const newItem = document.createElement('div');
        newItem.className = 'sale-item';
        newItem.innerHTML = `
            <div class="form-row">
                <div class="form-group">
                    <label>Product</label>
                    <select name="items[${itemCount}][inventory_id]" class="form-control item-select" required>
                        <option value="">Select Product</option>
                        ${document.querySelector('.item-select').innerHTML.replace(/selected="selected"/g, '')}
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" name="items[${itemCount}][quantity]" min="1" value="1" 
                           class="form-control item-quantity" required>
                </div>
                
                <div class="form-group">
                    <label>Unit Price</label>
                    <input type="number" name="items[${itemCount}][unit_price]" step="0.01" 
                           class="form-control item-price" readonly>
                </div>
                
                <div class="form-group">
                    <label>Total</label>
                    <input type="number" name="items[${itemCount}][total]" step="0.01" 
                           class="form-control item-total" readonly>
                </div>
                
                <div class="form-group">
                    <button type="button" class="btn btn-danger remove-item">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        
        document.getElementById('saleItems').appendChild(newItem);
        setupItemEvents(newItem);
        
        // Enable all remove buttons if we have more than one item
        if (document.querySelectorAll('.sale-item').length > 1) {
            document.querySelectorAll('.remove-item').forEach(btn => btn.disabled = false);
        }
    });

    // Setup events for initial item
    document.querySelectorAll('.sale-item').forEach(item => {
        setupItemEvents(item);
    });

    // Discount calculation
    document.getElementById('discount').addEventListener('input', updateTotals);

    function setupItemEvents(item) {
        const select = item.querySelector('.item-select');
        const quantity = item.querySelector('.item-quantity');
        const price = item.querySelector('.item-price');
        const total = item.querySelector('.item-total');
        const removeBtn = item.querySelector('.remove-item');

        select.addEventListener('change', function() {
            if (this.value) {
                const selectedOption = this.options[this.selectedIndex];
                price.value = selectedOption.dataset.price;
                total.value = (price.value * quantity.value).toFixed(2);
                updateTotals();
                
                // Validate quantity doesn't exceed stock
                quantity.max = selectedOption.dataset.stock;
            } else {
                price.value = '';
                total.value = '';
            }
        });

        quantity.addEventListener('input', function() {
            if (select.value && price.value) {
                total.value = (price.value * this.value).toFixed(2);
                updateTotals();
            }
        });

        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                item.remove();
                updateTotals();
                
                // Disable remove buttons if only one item left
                if (document.querySelectorAll('.sale-item').length === 1) {
                    document.querySelector('.remove-item').disabled = true;
                }
            });
        }
    }

    function updateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.item-total').forEach(input => {
            if (input.value) subtotal += parseFloat(input.value);
        });

        const discount = parseFloat(document.getElementById('discount').value) || 0;
        const discountAmount = subtotal * (discount / 100);
        const grandTotal = subtotal - discountAmount;

        document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
        document.getElementById('discountAmount').textContent = `$${discountAmount.toFixed(2)}`;
        document.getElementById('grandTotal').textContent = `$${grandTotal.toFixed(2)}`;
    }
}

// Initialize all functionality
document.addEventListener('DOMContentLoaded', () => {
    // Existing functionality...
    setupSalesForm();
    
    // Modal functionality
    const modal = document.getElementById('modal');
    if (modal) {
        const modalContent = modal.querySelector('.modal-content');
        const closeModal = modal.querySelector('.close-modal');
        
        // Close modal when clicking X or outside
        closeModal.addEventListener('click', () => modal.style.display = 'none');
        window.addEventListener('click', (e) => {
            if (e.target === modal) modal.style.display = 'none';
        });
        
        // Make modal draggable
        let isDragging = false;
        let offsetX, offsetY;
        
        modal.querySelector('.modal-content').addEventListener('mousedown', (e) => {
            if (e.target === closeModal) return;
            isDragging = true;
            offsetX = e.clientX - modalContent.getBoundingClientRect().left;
            offsetY = e.clientY - modalContent.getBoundingClientRect().top;
            modalContent.style.cursor = 'grabbing';
        });
        
        window.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            modalContent.style.left = `${e.clientX - offsetX}px`;
            modalContent.style.top = `${e.clientY - offsetY}px`;
        });
        
        window.addEventListener('mouseup', () => {
            isDragging = false;
            modalContent.style.cursor = '';
        });
    }
    
    // AJAX form submissions
    document.querySelectorAll('[data-ajax-form]').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            submitBtn.disabled = true;
            
            try {
                const formData = new FormData(this);
                const response = await fetch(this.action, {
                    method: this.method,
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast(result.message || 'Operation successful', 'success');
                    if (result.redirect) {
                        setTimeout(() => window.location.href = result.redirect, 1500);
                    } else if (result.reload) {
                        setTimeout(() => window.location.reload(), 1500);
                    }
                } else {
                    showToast(result.message || 'An error occurred', 'error');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            } catch (error) {
                console.error('Form submission error:', error);
                showToast('An error occurred. Please try again.', 'error');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    });
});

// Global modal function
function openModal(title, content, size = 'md') {
    const modal = document.getElementById('modal');
    if (!modal) return;
    
    const modalContent = modal.querySelector('.modal-content');
    const modalBody = modal.querySelector('.modal-body');
    
    modalContent.className = `modal-content modal-${size}`;
    modalBody.innerHTML = `
        <h2>${title}</h2>
        ${content}
    `;
    
    modal.style.display = 'flex';
    
    // Center modal
    modalContent.style.left = '50%';
    modalContent.style.top = '50%';
    modalContent.style.transform = 'translate(-50%, -50%)';
}

// Add to window object for global access
window.modal = {
    open: openModal,
    close: function() {
        document.getElementById('modal').style.display = 'none';
    }
};
// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    const mainNav = document.querySelector('.main-nav');
    
    if (mobileToggle && mainNav) {
        mobileToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            this.innerHTML = mainNav.classList.contains('active') ? 
                '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
        });
    }
    
    // Close alerts
    document.querySelectorAll('.close-alert').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.alert').style.opacity = '0';
            setTimeout(() => this.closest('.alert').remove(), 300);
        });
    });
    
    // Dropdown menus for mobile
    document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                const dropdown = this.nextElementSibling;
                dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
            }
        });
    });
});
// Add to global scope for easy access
window.mobileMenu = {
    toggle: function() {
        const mobileToggle = document.querySelector('.mobile-menu-toggle');
        const mainNav = document.querySelector('.main-nav');
        if (mobileToggle && mainNav) {
            mobileToggle.click();
        }
    }
};
// Add to existing script.js
// Initialize sales form functionality
document.addEventListener('DOMContentLoaded', () => {
    setupSalesForm();

    // Initialize tooltips
    const tooltips = document.querySelectorAll("[data-tooltip]");
    tooltips.forEach(el => {
        el.addEventListener("mouseenter", showTooltip);
        el.addEventListener("mouseleave", hideTooltip);
    });

    function showTooltip(e) {
        const tooltipText = this.getAttribute("data-tooltip");
        const tooltip = document.createElement("div");
        tooltip.className = "tooltip";
        tooltip.textContent = tooltipText;
        document.body.appendChild(tooltip);

        const rect = this.getBoundingClientRect();
        tooltip.style.left = `${rect.left + rect.width / 2 - tooltip.offsetWidth / 2}px`;
        tooltip.style.top = `${rect.top - tooltip.offsetHeight - 5}px`;

        this.tooltip = tooltip;
    }

    function hideTooltip() {
        if (this.tooltip) {
            this.tooltip.remove();
            this.tooltip = null;
        }
    }
});
// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    const mainNav = document.querySelector('.main-nav');
    
    if (mobileToggle && mainNav) {
        mobileToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            this.innerHTML = mainNav.classList.contains('active') ? 
                '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
        });
    }
    
    // Close alerts
    document.querySelectorAll('.close-alert').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.alert').style.opacity = '0';
            setTimeout(() => this.closest('.alert').remove(), 300);
        });
    });
    
    // Dropdown menus for mobile
    document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                const dropdown = this.nextElementSibling;
                dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
            }
        });
    });
});
