document.getElementById('searchButton').addEventListener('click', function () {
    const query = document.getElementById('query').value;
    axios.get('/search-locations', {
        params: {
            query: query
        }
    }).then(response => {
        console.log('Countries:', response.data.countries);
        console.log('Counties:', response.data.counties);
        console.log('Subcounties:', response.data.subcounties);
    }).catch(error => {
        console.error(error);
    });
});
