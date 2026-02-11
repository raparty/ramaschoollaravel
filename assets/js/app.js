// Runtime target: PHP 8.4+ / MySQL 8.4+
(() => {
    const activeLink = document.querySelector(`.app-sidebar .nav-link[href='${location.pathname.split('/').pop()}']`);
    if (activeLink) {
        activeLink.classList.add('active');
    }

    const runtimeBadge = document.querySelector('[data-runtime-badge="true"]');
    if (runtimeBadge) {
        const phpVersion = runtimeBadge.dataset.phpVersion;
        const mysqlVersion = runtimeBadge.dataset.mysqlVersion;
        runtimeBadge.title = `Running on PHP ${phpVersion} and MySQL ${mysqlVersion}`;
    }
})();

/**
 * AJAX helper function for dynamic form updates
 * Used by: student pages, exam pages, transport pages, fees pages
 * Loads content from a URL and updates a target element
 */
function getForm(url) {
    // Default target element if not specified
    const targetElement = document.getElementById('txtHint') || 
                         document.getElementById('result') || 
                         document.getElementById('ajax-content');
    
    if (!targetElement) {
        console.error('getForm: No target element found (txtHint, result, or ajax-content)');
        return;
    }
    
    // Show loading indicator
    targetElement.innerHTML = '<div style="padding: 20px; text-align: center;">Loading...</div>';
    
    // Use Fetch API instead of XMLHttpRequest
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(data => {
            targetElement.innerHTML = data;
        })
        .catch(error => {
            console.error('getForm error:', error);
            targetElement.innerHTML = `<div style="padding: 20px; color: #D13438;">Error loading content. Please try again.</div>`;
        });
}

/**
 * AJAX helper function for vehicle/route selection
 * Used by: transport pages
 * Similar to getForm but specifically for transport vehicle/route cascading
 */
function getVehicle(url) {
    const targetElement = document.getElementById('vehicle_list') || 
                         document.getElementById('txtHint') || 
                         document.getElementById('result');
    
    if (!targetElement) {
        console.error('getVehicle: No target element found');
        return;
    }
    
    targetElement.innerHTML = '<div style="padding: 10px;">Loading vehicles...</div>';
    
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(data => {
            targetElement.innerHTML = data;
        })
        .catch(error => {
            console.error('getVehicle error:', error);
            targetElement.innerHTML = `<div style="padding: 10px; color: #D13438;">Error loading vehicles.</div>`;
        });
}

/**
 * AJAX helper function for registration number validation
 * Used by: fees pages, student entry pages
 * Checks if registration number exists and displays student info
 */
function getCheckreg(url) {
    const targetElement = document.getElementById('reg_result') || 
                         document.getElementById('txtHint') || 
                         document.getElementById('result');
    
    if (!targetElement) {
        console.error('getCheckreg: No target element found');
        return;
    }
    
    targetElement.innerHTML = '<div style="padding: 10px; font-size: 12px; color: #666;">Checking...</div>';
    
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(data => {
            targetElement.innerHTML = data;
        })
        .catch(error => {
            console.error('getCheckreg error:', error);
            targetElement.innerHTML = `<div style="padding: 10px; font-size: 12px; color: #D13438;">Error checking registration.</div>`;
        });
}

/**
 * Generic AJAX content loader
 * Can be used as a replacement for inline event handlers
 * Usage: loadContent('ajax_file.php?param=value', 'target-element-id')
 */
function loadContent(url, targetId) {
    const targetElement = document.getElementById(targetId);
    
    if (!targetElement) {
        console.error(`loadContent: Target element #${targetId} not found`);
        return;
    }
    
    targetElement.innerHTML = '<div style="padding: 15px; text-align: center;">Loading...</div>';
    
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(data => {
            targetElement.innerHTML = data;
        })
        .catch(error => {
            console.error('loadContent error:', error);
            targetElement.innerHTML = `<div style="padding: 15px; color: #D13438; text-align: center;">Error loading content.</div>`;
        });
}

/**
 * Modern event listener setup for cascading dropdowns
 * Usage: setupCascadingDropdown('class_id', 'section_dropdown', 'ajax_stream_code.php')
 */
function setupCascadingDropdown(sourceId, targetId, ajaxFile) {
    const sourceElement = document.getElementById(sourceId);
    
    if (!sourceElement) {
        console.error(`setupCascadingDropdown: Source element #${sourceId} not found`);
        return;
    }
    
    sourceElement.addEventListener('change', function() {
        const url = `${ajaxFile}?${sourceId}=${this.value}`;
        loadContent(url, targetId);
    });
}
