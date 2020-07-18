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

export const findBeer = async(beer_ids) => {
    try {
        const response = await fetch('/api/find-by-id?beer_ids=' + beer_ids);
        return response.json();
    } catch (error) {
        throw error;
    }
}
