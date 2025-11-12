export async function httpRequest(url, options = {}) {
    let config = {
        method: options.method || "GET",
        headers: options.headers || {},
        credentials: options.credentials || "include",
    };

    if (options.body instanceof FormData) {
        // Don't set Content-Type header for FormData!
        config.body = options.body;
    } else if (options.body) {
        config.headers["Content-Type"] = "application/json";
        config.body = JSON.stringify(options.body);
    }

    try {
        const response = await fetch(url, config);
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || "Request failed");
        }
        return await response.json();
    } catch (error) {
        console.error("HTTP Request Error:", error);
        throw error;
    }
}
