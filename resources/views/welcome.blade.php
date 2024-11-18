<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Locations</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <h1>Search Locations</h1>

    <!-- Dropdown for Countries -->
    <label for="country">Select Country:</label>
    <select id="country" onchange="fetchCounties()">
        <option value="">-- Select a Country --</option>
    </select>

    <!-- Dropdown for Counties -->
    <label for="county">Select County:</label>
    <select id="county" onchange="fetchSubcounties()">
        <option value="">-- Select a County --</option>
    </select>

    <!-- Dropdown for Subcounties -->
    <label for="subcounty">Select Subcounty:</label>
    <!-- <select id="subcounty">
        <option value="">-- Select a Subcounty --</option>
    </select> -->


    <select id="subcounty" onchange="fetchLocationDetails()">
    <option value="">-- Select a Subcounty --</option>
</select>
<div>
<!-- Search Box -->
<label for="search">Search </label>
<input type="text" id="search" placeholder="Enter country or subcounty name" oninput="searchLocations()">
</div>
<!-- Results -->
<div id="results">
    <!-- Results will be populated here -->
</div>



    <script>
    // Fetch countries on page load
    window.onload = function () {
        axios.get('/search-locations').then(response => {
            const countries = response.data.countries;
            const countryDropdown = document.getElementById('country');
            countries.forEach(country => {
                const option = document.createElement('option');
                option.value = country.id;
                option.textContent = country.name;
                countryDropdown.appendChild(option);
            });
        }).catch(error => {
            console.error('Error fetching countries:', error);
        });
    };

    // Fetch counties when a country is selected
    function fetchCounties() {
        const countryId = document.getElementById('country').value;
        const countyDropdown = document.getElementById('county');
        countyDropdown.innerHTML = '<option value="">-- Select a County --</option>';
        document.getElementById('subcounty').innerHTML = '<option value="">-- Select a Subcounty --</option>';

        if (countryId) {
            axios.get('/search-locations', {
                params: { country_id: countryId }
            }).then(response => {
                const counties = response.data.counties;
                counties.forEach(county => {
                    const option = document.createElement('option');
                    option.value = county.id;
                    option.textContent = county.name;
                    countyDropdown.appendChild(option);
                });
            }).catch(error => {
                console.error('Error fetching counties:', error);
            });
        }
    }

    // Fetch subcounties when a county is selected
    function fetchSubcounties() {
        const countyId = document.getElementById('county').value;
        const subcountyDropdown = document.getElementById('subcounty');
        subcountyDropdown.innerHTML = '<option value="">-- Select a Subcounty --</option>';

        if (countyId) {
            axios.get('/search-locations', {
                params: { county_id: countyId }
            }).then(response => {
                const subcounties = response.data.subcounties;
                subcounties.forEach(subcounty => {
                    const option = document.createElement('option');
                    option.value = subcounty.id;
                    option.textContent = subcounty.name;
                    subcountyDropdown.appendChild(option);
                });

                // Display associated country and county
                const country = response.data.country;
                if (country) alert(`Country: ${country.name}`);
            }).catch(error => {
                console.error('Error fetching subcounties:', error);
            });
        }
    }

    function searchLocations() {
    const query = document.getElementById('search').value.trim();
    const resultsDiv = document.getElementById('results');
    resultsDiv.innerHTML = ''; // Clear previous results

    if (query) {
        axios.get('/search-locations', {
            params: { query }
        }).then(response => {
            const countries = response.data.countries || [];
            const counties = response.data.counties || [];
            const subcounties = response.data.subcounties || [];

            // Display countries
            if (countries.length) {
                const countryHeader = document.createElement('h3');
                countryHeader.textContent = 'Countries:';
                resultsDiv.appendChild(countryHeader);

                countries.forEach(country => {
                    const countryItem = document.createElement('div');
                    countryItem.textContent = `Country: ${country.name}`;
                    resultsDiv.appendChild(countryItem);

                    // Show counties and subcounties for this country
                    const countryCounties = counties.filter(c => c.country_id === country.id);
                    if (countryCounties.length) {
                        countryCounties.forEach(county => {
                            const countyItem = document.createElement('div');
                            countyItem.textContent = `  County: ${county.name}`;

                            // Show subcounties
                            const countySubcounties = subcounties.filter(s => s.county_id === county.id);
                            if (countySubcounties.length) {
                                countySubcounties.forEach(subcounty => {
                                    const subcountyItem = document.createElement('div');
                                    subcountyItem.textContent = `    Subcounty: ${subcounty.name}`;
                                    countyItem.appendChild(subcountyItem);
                                });
                            }
                            countryItem.appendChild(countyItem);
                        });
                    }
                    resultsDiv.appendChild(countryItem);
                });
            }

            // Display subcounties directly searched
            if (subcounties.length) {
                const subcountyHeader = document.createElement('h3');
                subcountyHeader.textContent = 'Subcounties:';
                resultsDiv.appendChild(subcountyHeader);

                subcounties.forEach(subcounty => {
                    const subcountyItem = document.createElement('div');
                    subcountyItem.textContent = `Subcounty: ${subcounty.name}`;
                    resultsDiv.appendChild(subcountyItem);

                    // Find and display related county
                    const relatedCounty = counties.find(c => c.id === subcounty.county_id);
                    if (relatedCounty) {
                        const countyItem = document.createElement('div');
                        countyItem.textContent = `  County: ${relatedCounty.name}`;
                        subcountyItem.appendChild(countyItem);

                        // Find and display related country
                        const relatedCountry = countries.find(c => c.id === relatedCounty.country_id);
                        if (relatedCountry) {
                            const countryItem = document.createElement('div');
                            countryItem.textContent = `    Country: ${relatedCountry.name}`;
                            subcountyItem.appendChild(countryItem);
                        }
                    }
                });
            }
        }).catch(error => {
            console.error('Error searching locations:', error);
        });
    }
}

    // Fetch location details when a subcounty is selected
    function fetchLocationDetails() {
        const subcountyId = document.getElementById('subcounty').value;

        if (subcountyId) {
            axios.get('/search-locations', {
                params: { subcounty_id: subcountyId }
            }).then(response => {
                const subcounty = response.data.subcounty;
                const county = response.data.county;
                const country = response.data.country;

                if (country && county) {
                    alert(`Subcounty: ${subcounty.name}\nCounty: ${county.name}\nCountry: ${country.name}`);
                }
            }).catch(error => {
                console.error('Error fetching location details:', error);
            });
        }
    }
</script>
<style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
            background-color: #f9f9f9;
        }

        h1 {
            text-align: center;
            color: #4CAF50;
        }

        label {
            font-weight: bold;
            margin-right: 10px;
        }

        select, input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        select:focus, input[type="text"]:focus {
            border-color: #4CAF50;
            outline: none;
        }

        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        #results {
            margin-top: 20px;
            padding: 10px;
            background: #f0f4f8;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .result-item {
            padding: 5px 0;
            border-bottom: 1px solid #ddd;
        }

        .result-item:last-child {
            border-bottom: none;
        }

        .alert {
            color: #555;
            padding: 10px;
            background: #e7f3fe;
            border: 1px solid #b3d4fc;
            border-radius: 5px;
        }

        .no-results {
            text-align: center;
            color: #999;
            font-style: italic;
        }
    </style>
</body>
</html>
