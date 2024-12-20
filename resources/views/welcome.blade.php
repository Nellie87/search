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

    <label for="search" class="search-label">Search Any Location:</label>
<div class="search-container">
    <input type="text" id="search" class="search-input" placeholder="Enter a country, county, subcounty, location, or sublocation">
    <button onclick="traceHierarchy()" class="search-button">Search</button>
</div>

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

<!-- <label for="location">Select Location:</label>
    <select id="location">
        <option value="">-- Select a Location --</option>
    </select> -->

    <label for="location">Select Location:</label>
    <select id="location" onchange="fetchSublocations()">
    <option value="">-- Select a Location --</option>
</select>

    <label for="sublocation">Select Sublocation:</label>
<select id="sublocation">
    <option value="">-- Select a Sublocation --</option>
</select>







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

    function fetchLocations() {
        const subcountyId = document.getElementById('subcounty').value;
        const locationDropdown = document.getElementById('location');
        locationDropdown.innerHTML = '<option value="">-- Select a Location --</option>';

        if (subcountyId) {
            axios.get('/search-locations', {
                params: { subcounty_id: subcountyId }
            }).then(response => {
                const locations = response.data.locations;
                locations.forEach(location => {
                    const option = document.createElement('option');
                    option.value = location.id;
                    option.textContent = location.name;
                    locationDropdown.appendChild(option);
                });
            }).catch(error => {
                console.error('Error fetching locations:', error);
            });
        }
    }
    function fetchSublocations() {
    const locationId = document.getElementById('location').value;
    const sublocationDropdown = document.getElementById('sublocation');
    sublocationDropdown.innerHTML = '<option value="">Loading...</option>';

    if (locationId) {
        axios.get('/search-locations', {
            params: { location_id: locationId }
        }).then(response => {
            const sublocations = response.data.sublocations;
            sublocationDropdown.innerHTML = '<option value="">-- Select a Sublocation --</option>';
            if (sublocations.length > 0) {
                sublocations.forEach(sublocation => {
                    const option = document.createElement('option');
                    option.value = sublocation.id;
                    option.textContent = sublocation.name;
                    sublocationDropdown.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.value = "";
                option.textContent = "No sublocations available";
                sublocationDropdown.appendChild(option);
            }
        }).catch(error => {
            console.error('Error fetching sublocations:', error);
            sublocationDropdown.innerHTML = '<option value="">Error loading sublocations</option>';
        });
    }
}

    function fetchLocationDetails() {
    const subcountyId = document.getElementById('subcounty').value;
    const locationDropdown = document.getElementById('location');
    locationDropdown.innerHTML = '<option value="">-- Select a Location --</option>';

    if (subcountyId) {
        axios.get('/search-locations', {
            params: { subcounty_id: subcountyId }
        }).then(response => {
            const locations = response.data.locations;
            locations.forEach(location => {
                const option = document.createElement('option');
                option.value = location.id;
                option.textContent = location.name;
                locationDropdown.appendChild(option);
            });
        }).catch(error => {
            console.error('Error fetching locations:', error);
        });
    }
}
function traceHierarchy() {
    const query = document.getElementById('search').value;

    if (!query) {
        alert('Please enter a name to search.');
        return;
    }

    axios.get('/trace-hierarchy', { params: { query } })
        .then(response => {
            const data = response.data;

            // Update dropdowns with the traced hierarchy
            updateDropdown('country', data.country, data.level === 'country');
            updateDropdown('county', data.county, data.level === 'county');
            updateDropdown('subcounty', data.subcounty, data.level === 'subcounty');
            updateDropdown('location', data.location, data.level === 'location');
            updateDropdown('sublocation', data.sublocation, data.level === 'sublocation');

            alert(`Search completed: Found at level - ${data.level}`);
        })
        .catch(error => {
            if (error.response && error.response.status === 404) {
                alert('No matching records found.');
            } else {
                console.error('Error tracing hierarchy:', error);
                alert('An error occurred while tracing the hierarchy.');
            }
        });
}

// Utility function to update dropdowns
function updateDropdown(dropdownId, value, isExactMatch) {
    const dropdown = document.getElementById(dropdownId);
    dropdown.innerHTML = ''; // Clear existing options

    // Add the current value to the dropdown
    const option = document.createElement('option');
    option.value = isExactMatch ? '' : 'unknown';
    option.textContent = value;
    dropdown.appendChild(option);

    // Disable dropdown if it's already selected
    dropdown.disabled = isExactMatch;

    // Enable and load options dynamically if "Unknown" or not exact match
    if (value === 'Unknown' || !isExactMatch) {
        dropdown.disabled = false;
        loadOptions(dropdownId); // Load options dynamically
    }
}

// Function to fetch and load options into a dropdown
function loadOptions(level) {
    axios.get(`/get-${level}-options`)
        .then(response => {
            const dropdown = document.getElementById(level);
            const options = response.data.options;

            // Populate dropdown with options
            options.forEach(optionData => {
                const option = document.createElement('option');
                option.value = optionData.id;
                option.textContent = optionData.name;
                dropdown.appendChild(option);
            });

            dropdown.disabled = false; // Enable the dropdown after populating
        })
        .catch(error => {
            console.error(`Error loading ${level} options:`, error);
        });
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
        .search-container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-top: 20px;
    }

    /* Styling for the label */
    .search-label {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-right: 10px;
        align-self: center;
    }

    /* Styling for the input field */
    .search-input {
        width: 300px;
        padding: 12px 15px;
        font-size: 16px;
        border-radius: 5px;
        border: 1px solid #ccc;
        transition: border-color 0.3s ease;
    }

    /* Focus effect for the input field */
    .search-input:focus {
        border-color: #4CAF50;
        outline: none;
    }

    /* Styling for the button */
    .search-button {
        padding: 12px 20px;
        font-size: 16px;
        color: white;
        background-color: #4CAF50;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    /* Hover effect for the button */
    .search-button:hover {
        background-color: #45a049;
    }

    /* Optional: Add spacing to the container */
    .search-container {
        margin: 20px auto;
        max-width: 500px;
    }
    </style>
</body>
</html>
