export const randomBeers = async () => {
    try {
        const response = await fetch('/api/random');
        return response.json();
    } catch (error) {
        throw error;
    }
}
