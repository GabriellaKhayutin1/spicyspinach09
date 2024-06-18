document.addEventListener('DOMContentLoaded', function () {
    const clickedMarkers = [];
    const recentClicks = [];
    const allMarkers = [];
    const favouritesArray = [];
    const leaflet = L;
    const popup = L.popup();
    let selectedFeature = null;

    const corner1 = L.latLng(50.7504, 3.3316),
        corner2 = L.latLng(53.5587, 7.2271);
    const bounds = L.latLngBounds(corner1, corner2);

    const map = leaflet.map('map', {
        center: [52.1326, 5.2913],
        zoom: 7,
        maxBounds: bounds,
        maxBoundsViscosity: 1.0
    });

    leaflet.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    fetch('/fetch-geojson')
        .then(response => response.json())
        .then(data => {
            if (data.file) {
                fetch(`/geopackages/${data.file}`)
                    .then(response => response.json())
                    .then(geoJsonData => {
                        console.log('GeoJSON data:', geoJsonData); // Log the data
                        addGeoJsonPoints(geoJsonData, map);
                    })
                    .catch(error => {
                        showError('Error fetching GeoJSON data. Please try again later.');
                    });
            } else {
                showError('No GeoJSON file found. Please upload a file.');
            }
        })
        .catch(error => {
            showError('Error fetching GeoJSON file name. Please try again later.');
        });

    function showError(message) {
        const errorMessage = document.createElement('div');
        errorMessage.className = 'error-message';
        errorMessage.textContent = message;
        document.body.appendChild(errorMessage);
        setTimeout(() => {
            errorMessage.style.display = 'none';
        }, 5000);
    }

    const geojsonMarkerOptions = {
        radius: 8,
        fillColor: "white",
        color: "#000",
        weight: 1,
        opacity: 0.5,
        fillOpacity: 0.8
    };

    function addGeoJsonPoints(geoJsonData, map) {
        L.geoJSON(geoJsonData, {
            pointToLayer: function (feature, latlng) {
                const marker = L.circleMarker(latlng, geojsonMarkerOptions);
                marker.feature = feature; // Attach feature to marker for later reference

                marker.on('click', function (e) {
                    const properties = feature.properties;
                    const popupContent = `<b>ID:</b> ${properties.statcode}<br/><b>Name:</b> ${properties.statnaam}<br/><b>Coordinates:</b> ${latlng.lat}, ${latlng.lng}<br/>`;

                    if (clickedMarkers.includes(marker)) {
                        const index = clickedMarkers.indexOf(marker);
                        if (index !== -1) {
                            clickedMarkers.splice(index, 1);
                            marker.setStyle({ fillColor: "white" });
                            map.closePopup();
                            // Remove the item from the sidebar
                            const items = document.querySelectorAll('#item-list .list-item');
                            items.forEach(item => {
                                if (item.getAttribute('data-id') === properties.statcode) {
                                    item.remove();
                                }
                            });
                        }
                    } else {
                        clickedMarkers.push(marker);
                        marker.setStyle({ fillColor: "#0000ff" });
                        popup.setLatLng(latlng).setContent(popupContent).openOn(map);

                        // Add the item to the sidebar
                        const sidebarContent = `<li class="list-item" data-id="${properties.statcode}" data-name="${properties.statnaam}" data-coordinates="${latlng.lat},${latlng.lng}"><b>ID:</b> ${properties.statcode}<br/><b>Name:</b> ${properties.statnaam}<br/><b>Coordinates:</b> ${latlng.lat}, ${latlng.lng}<br/><button class="remove-button button">Remove</button><button class="download-individual-button button">Download</button><button class="favourite-button button">Favorite</button></li>`;
                        document.getElementById('item-list').insertAdjacentHTML('beforeend', sidebarContent);

                        // Attach event listeners to buttons after adding them to the DOM
                        attachButtonEventListeners();

                        // Store the recent click
                        recentClicks.push({
                            id: properties.statcode,
                            name: properties.statnaam,
                            coordinates: [latlng.lat, latlng.lng]
                        });
                    }
                });
                allMarkers.push(marker); // Store marker in allMarkers array
                return marker;
            }
        }).addTo(map);
    }

    function attachButtonEventListeners() {
        // Add event listener to the remove button
        document.querySelectorAll('.remove-button').forEach(button => {
            button.addEventListener('click', function () {
                const listItem = this.parentElement;
                const itemId = listItem.getAttribute('data-id');

                // Remove the corresponding marker
                const markerIndex = clickedMarkers.findIndex(m => m.feature.properties.statcode === itemId);
                if (markerIndex !== -1) {
                    clickedMarkers[markerIndex].setStyle({ fillColor: "white" });
                    clickedMarkers.splice(markerIndex, 1);
                }

                // Remove the item from recentClicks array
                const recentIndex = recentClicks.findIndex(item => item.id === itemId);
                if (recentIndex !== -1) {
                    recentClicks.splice(recentIndex, 1);
                }

                // Remove the item from the sidebar
                listItem.remove();
            });
        });

        // Add event listener to the download button
        document.querySelectorAll('.download-individual-button').forEach(button => {
            button.addEventListener('click', function () {
                const listItem = this.parentElement;
                const id = listItem.getAttribute('data-id');
                const name = listItem.getAttribute('data-name');
                const coordinates = listItem.getAttribute('data-coordinates');

                // Create the data object with all details
                const dataToDownload = { id, name, coordinates };

                const blob = new Blob([JSON.stringify(dataToDownload, null, 2)], { type: 'application/geo+json' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `${id}_data.geojson`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);

                // Change the download icon
                this.textContent = 'Downloaded';
                this.style.backgroundColor = '#28a745'; // Green color
            });
        });

        // Add event listener to the favorite button
        document.querySelectorAll('.favourite-button').forEach(button => {
            button.addEventListener('click', function () {
                const listItem = this.parentElement;
                const itemId = listItem.getAttribute('data-id');
                const itemName = listItem.getAttribute('data-name');
                const itemCoordinates = listItem.getAttribute('data-coordinates');

                // Check if item already exists in favouritesArray
                const existingIndex = favouritesArray.findIndex(item => item.id === itemId);
                if (existingIndex === -1) {
                    // Add to favouritesArray
                    favouritesArray.push({
                        id: itemId,
                        name: itemName,
                        coordinates: itemCoordinates.split(',')
                    });
                    // Change button style to indicate item is favourited
                    this.style.backgroundColor = 'pink';
                } else {
                    // Item already favourited, remove from favourites
                    favouritesArray.splice(existingIndex, 1);
                    // Change button style to indicate item is no longer favourited
                    this.style.backgroundColor = '';
                }

                // Update the favorites list if currently viewing the favorites tab
                if (document.getElementById('favourite-button').classList.contains('active-button')) {
                    renderList(favouritesArray);
                }
            });
        });
    }

    function setActiveButton(activeButtonId) {
        const buttons = document.querySelectorAll('#favourite-button, #recent-button, #select-button');
        buttons.forEach(button => {
            if (button.id === activeButtonId) {
                button.classList.add('active-button');
            } else {
                button.classList.remove('active-button');
            }
        });
    }

    // Reset button functionality
    document.getElementById('reset-button').addEventListener('click', function () {
        // Clear the sidebar
        document.getElementById('item-list').innerHTML = '';
        // Reset marker styles
        clickedMarkers.forEach(marker => {
            marker.setStyle({ fillColor: "white" });
        });
        // Clear the clickedMarkers array
        clickedMarkers.length = 0;

        // Reset download button text and color
        const downloadButton = document.getElementById('download-button');
        downloadButton.textContent = 'Download selected buurtens';
        downloadButton.style.backgroundColor = ''; // Reset to default color
        // Clear the stored button state
        localStorage.removeItem('downloadButtonClicked');
    });

    // Recent button functionality
    document.getElementById('recent-button').addEventListener('click', function () {
        renderList(recentClicks);
        setActiveButton('recent-button');
    });

    // Favorites button functionality
    document.getElementById('favourite-button').addEventListener('click', function () {
        renderList(favouritesArray);
        setActiveButton('favourite-button');
    });

    // Selection button functionality (if needed)
    document.getElementById('select-button').addEventListener('click', function () {
        renderList(clickedMarkers.map(marker => ({
            id: marker.feature.properties.statcode,
            name: marker.feature.properties.statnaam,
            coordinates: [marker.getLatLng().lat, marker.getLatLng().lng]
        })));
        setActiveButton('select-button');
    });

    // Add functionality to the global download button
    const downloadButton = document.getElementById('download-button');
    const downloadStatusText = document.createElement('div');
    downloadStatusText.id = 'download-status';
    downloadButton.parentNode.insertBefore(downloadStatusText, downloadButton.nextSibling);

    downloadButton.addEventListener('click', function () {
        const items = document.querySelectorAll('#item-list .list-item');
        if (items.length === 0) {
            alert('No files chosen to be downloaded');
            return;
        }

        const dataToDownload = [];
        items.forEach(item => {
            const id = item.getAttribute('data-id');
            const name = item.getAttribute('data-name');
            const coordinates = item.getAttribute('data-coordinates');

            // Push the full data object to the array
            dataToDownload.push({ id, name, coordinates });
        });

        const blob = new Blob([JSON.stringify(dataToDownload, null, 2)], { type: 'application/geo+json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'selected_data.geojson';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        // Change the button color and text
        downloadButton.textContent = 'The selected files have been downloaded';
        downloadButton.style.backgroundColor = '#28a745'; // Green color
        // Update the status text
        // downloadStatusText.textContent = 'The selected files have been downloaded';
        // Store the button state in localStorage
        localStorage.setItem('downloadButtonClicked', true);
    });

    // Check button state on page load
    if (localStorage.getItem('downloadButtonClicked')) {
        downloadButton.textContent = 'Downloaded';
        downloadButton.style.backgroundColor = '#28a745'; // Green color
        // document.getElementById('download-status').textContent = 'The selected files have been downloaded';
    }

    // Reset button color on page refresh
    window.addEventListener('beforeunload', function () {
        localStorage.removeItem('downloadButtonClicked');
    });

    // Function to render list items in the sidebar
    function renderList(list) {
        // Clear the sidebar
        document.getElementById('item-list').innerHTML = '';
        // Add list items to the sidebar
        list.forEach((item) => {
            const itemContent = `<li class="list-item" data-id="${item.id}" data-name="${item.name}" data-coordinates="${item.coordinates[0]},${item.coordinates[1]}"><b>ID:</b> ${item.id}<br/><b>Name:</b> ${item.name}<br/><b>Coordinates:</b> ${item.coordinates[0]}, ${item.coordinates[1]}<br/><button class="remove-button button">Remove</button><button class="download-individual-button button">Download</button><button class="favourite-button button ${favouritesArray.find(fav => fav.id === item.id) ? 'favourited' : ''}">Favorite</button></li>`;
            document.getElementById('item-list').insertAdjacentHTML('beforeend', itemContent);

            // Add event listeners to buttons
            attachButtonEventListeners();
        });
    }

    // Search bar functionality
    const searchBar = document.getElementById('search-bar');
    searchBar.addEventListener('input', function () {
        const query = searchBar.value.toLowerCase();

        allMarkers.forEach(marker => {
            const { statcode, statnaam } = marker.feature.properties;
            const match = statcode.toLowerCase().includes(query) || statnaam.toLowerCase().includes(query);
        });
    });

    // Event listener for Enter key press in search bar
    searchBar.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            const query = searchBar.value.toLowerCase();
            let firstMatch = null;

            allMarkers.forEach(marker => {
                const { statcode, statnaam } = marker.feature.properties;
                const match = statcode.toLowerCase().includes(query) || statnaam.toLowerCase().includes(query);

                if (match && !firstMatch) {
                    firstMatch = marker;
                }

                if (match) {
                    marker.addTo(map);
                    marker.setStyle({ fillColor: "#ff0000" });// Show matching markers
                } else {
                    console.log('4')
                }
            });

            // Open a popup for the first match
            if (firstMatch) {
                const { statcode, statnaam } = firstMatch.feature.properties;
                const latlng = firstMatch.getLatLng();
                const popupContent = `<b>ID:</b> ${statcode}<br/><b>Name:</b> ${statnaam}<br/><b>Coordinates:</b> ${latlng.lat}, ${latlng.lng}<br/>`;

                popup.setLatLng(latlng).setContent(popupContent).openOn(map);

            }
        }
    });
});
