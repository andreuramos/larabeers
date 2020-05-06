export const randomBeers = async () => {
    try {
        const response = await fetch('/api/random');
        return response.json();
    } catch (error) {
        throw error;
    }
}

export const searchBeers = async (query) => {
    try {
        const response = await fetch('/api/search?query=' + query);
        return response.json();
    } catch (error) {
        throw error;
    }
}
