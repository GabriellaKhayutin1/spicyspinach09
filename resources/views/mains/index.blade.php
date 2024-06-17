<x-layout>
    <style>
        .button {
            padding: 10px 15px;
            margin: 5px;
            border-radius: 5px;
            cursor: pointer;
            display: inline-block;
            text-align: center;
            border: none;
            font-size: 14px;
        }

        .remove-button {
            background-color: red;
            color: white;
        }
.return-button button{
    color: #4CAF50;
}
        .download-button, .download-individual-button {
            background-color: #f0f0f0;
            color: black;
        }

        .favourite-button {
            background-color: #f0f0f0;
            color: black;
        }

        .favourited {
            background-color: pink;
        }

        .active-button {
            background-color: orange;
            color: white;
        }
        .green-button {
            background-color: green;
            color: white;
        }
    </style>

    <div class="map-container">
        <div class="map" id="map"></div>
        <div class="file-search-container">
            <div class="file-box box" style="width: 100%; max-width: 500px;">
                <div class="current-file" id="currentFile" style="border: none;"></div>
                <br>
                <script>
                    function fetchCurrentFile() {
                        fetch('{{ route("fetch.geojson") }}')
                            .then(response => response.json())
                            .then(data => {
                                const currentFileDiv = document.getElementById('currentFile');
                                if (data.file) {
                                    currentFileDiv.innerHTML = `Currently selected: <strong>${data.file}</strong>`;
                                } else {
                                    currentFileDiv.innerHTML = 'No file currently uploaded.';
                                }
                            });
                    }

                    document.addEventListener('DOMContentLoaded', function() {
                        fetchCurrentFile();
                    });
                </script>
                <div class="container">
                    <form id="uploadForm" method="POST" action="{{ route('upload.post') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3 upload-file" style="display: flex; align-items: center;">
                            <input class="form-control button" name="file" type="file" id="formFile" style="display: none;" accept=".geojson">
                            <label for="formFile" class="form-control button is-success is-dark is-normal" style="margin-right: 10px;">Upload File</label>
                            <span class="has-text-black is-underlined has-text-weight-bold" id="fileChosen" style="margin-right: 10px;">No file chosen</span>
                            <input type="submit" class="return-button button green-button" value="Submit" style="height: auto; padding: 10px;">
                            <div class="error-message" id="errorMessage" style="color: red; display: none; margin-left: 10px;">Please choose a geojson file before submitting.</div>
                        </div>
                        <script>
                            const fileInput = document.getElementById('formFile');
                            const fileChosen = document.getElementById('fileChosen');
                            const errorMessage = document.getElementById('errorMessage');
                            const uploadForm = document.getElementById('uploadForm');

                            function hideErrorMessage() {
                                setTimeout(() => {
                                    errorMessage.style.display = 'none';
                                }, 3000);
                            }

                            fileInput.addEventListener('change', function() {
                                const file = fileInput.files[0];
                                if (file) {
                                    const fileName = file.name;
                                    const fileExtension = fileName.split('.').pop().toLowerCase();
                                    if (fileExtension === 'geojson') {
                                        fileChosen.textContent = fileName;
                                        errorMessage.style.display = 'none';
                                    } else {
                                        fileChosen.textContent = 'No file chosen';
                                        errorMessage.style.display = 'block';
                                        errorMessage.textContent = 'Invalid file type. Please choose a .geojson file.';
                                        hideErrorMessage();
                                    }
                                } else {
                                    fileChosen.textContent = 'No file chosen';
                                }
                            });

                            uploadForm.addEventListener('submit', function(event) {
                                if (!fileInput.value || !fileInput.files[0].name.endsWith('.geojson')) {
                                    event.preventDefault();
                                    errorMessage.style.display = 'block';
                                    errorMessage.textContent = 'Please choose a .geojson file before submitting.';
                                    hideErrorMessage();
                                }
                            });
                        </script>
                    </form>
                </div>
            </div>
            <div class="controls box">
                <input type="text" class="search-bar" id="search-bar" placeholder="Search by ID or Name">
                <div>
                    <button class="button" id="favourite-button">Favorites</button>
                    <button class="button" id="recent-button">Recents</button>
                    <button class="button" id="select-button">Selection</button>
                    <button class="button" id="reset-button">Reset</button>
                </div>
                <div class="scroll-container">
                    <ul class="list" id="item-list"></ul>
                </div>
                <button id="download-button" class="button download-button is-success">Download selected buurtens</button>
            </div>
        </div>
    </div>

    <!-- Success Login Modal -->
    <div class="modal" id="success-login-modal">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Success</p>
                <button class="delete" aria-label="close" id="close-success-login-modal"></button>
            </header>
            <section class="modal-card-body">
                <p>You have successfully logged in!</p>
            </section>
            <footer class="modal-card-foot">
                <button class="button is-success" id="close-success-login-button">OK</button>
            </footer>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Assign the session status to a JavaScript variable
            var loginStatus = <?php echo session('status') === 'You have successfully logged in.' ? 'true' : 'false'; ?>;
        })

        // Check the login status and show the modal if necessary
        if (loginStatus) {
            document.getElementById('success-login-modal').classList.add('is-active');
        }

        // Add event listeners to close the modal
        document.getElementById('close-success-login-modal').addEventListener('click', function() {
            document.getElementById('success-login-modal').classList.remove('is-active');
        });

        document.getElementById('close-success-login-button').addEventListener('click', function() {
            document.getElementById('success-login-modal').classList.remove('is-active');
        });
    </script>
</x-layout>
