export async function httpRequest(url, options = {}) {
    let config = {
        method: options.method || "GET",
        headers: options.headers || {},
        credentials: options.credentials || "include",
    };

    if (options.body instanceof FormData) {
        config.body = options.body;
    } else if (options.body) {
        config.headers["Content-Type"] = "application/json";
        config.body = JSON.stringify(options.body);
    }

    async function fetchWithRefresh() {
        const response = await fetch(url, config);
        if (response.status === 401) {
            if (url != "/api/auth/login") {
                const refreshed = await refreshToken();
                if (refreshed) {
                    return await fetch(url, config);
                } else {
                    showToast("error", "Session expired, please log in again.");
                    throw new Error("Session expired");
                }
            }
        }
        return response;
    }

    try {
        let response = await fetchWithRefresh();

        if (!response.ok) {
            const errorData = await response.json();
            showToast("error", errorData.message);
            throw new Error(errorData.message || "Request failed");
        }

        return await response.json();
    } catch (error) {
        console.error("HTTP Request Error:", error);
        throw error;
    }
}

async function refreshToken() {
    try {
        const res = await fetch('/api/auth/refresh', {
            method: "POST",
            credentials: "include",
        });
        return res.ok;
    } catch {
        return false;
    }
}


export function showAlert(type, text) {
    const defaultTitles = {
        success: 'Success!',
        error: 'Error!',
        warning: 'Warning!',
        info: 'Info',
        question: 'Question',
    };
    const title = defaultTitles[type] || 'Notice';

    Swal.fire({
        icon: type,         // 'success', 'error', 'warning', 'info', 'question'
        title: title,
        text: text || '',  
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.reload();
        }
    });
}

export function showToast(type, text) {
    Swal.fire({
        toast: true,
        position: 'top-end',      // 'top-end' is top right, 'top-start' is top left, etc.
        icon: type,               // 'success', 'error', 'warning', 'info', 'question'
        title: text,
        showConfirmButton: false, 
        timer: 3000,              
        timerProgressBar: true,
    });
}

export function showConfirmAlert(type, text, confirmText = 'Yes', cancelText = 'Cancel') {
    return Swal.fire({
        icon: type,           // 'warning', 'question', etc.
        title: text,
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        reverseButtons: true
    });
}


