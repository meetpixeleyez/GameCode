<script src="{{ asset('assets/global/js/wavesurfer.min.js') }}"></script>

@push('script')
    <script>
        (function($) {
            "use strict";

            // Store player configurations instead of initialized instances
            const playerConfigs = [];
            const activeWavesurfers = {};

            // Function to collect all player configurations
            function collectPlayerConfigs() {
                $("[id^=waveform-]").each(function() {
                    var id = $(this).attr("id").split("-")[1];

                    // Skip if config already exists
                    if (playerConfigs.find(p => p.id === id)) {
                        return;
                    }

                    // Store configuration only
                    playerConfigs.push({
                        id: id,
                        container: "#waveform-" + id,
                        playButton: "#play-button-" + id,
                        currentTimeEl: "#current-time-" + id,
                        totalTimeEl: "#total-time-" + id,
                        filePath: $(this).attr("data-file-path"),
                        tabId: $(this).closest('.tab-content').attr('id') || 'default-tab'
                    });
                });
            }

            // Function to create and initialize a WaveSurfer instance
            function createWaveSurfer(config) {
                // Destroy existing instance if it exists
                if (activeWavesurfers[config.id]) {
                    activeWavesurfers[config.id].destroy();
                    delete activeWavesurfers[config.id];
                }

                // Create new instance
                var wavesurfer = WaveSurfer.create({
                    container: config.container,
                    waveColor: "rgba(0, 0, 0, 0.7)",
                    progressColor: hslToHex(
                        window.getComputedStyle(document.body).getPropertyValue("--base")
                    ),
                    backend: "MediaElement",
                    height: 60,
                    barWidth: 2,
                    responsive: true
                });

                // Set up events
                wavesurfer.on("ready", function() {
                    $(config.totalTimeEl).text(formatTime(wavesurfer.getDuration()));
                });

                wavesurfer.on("audioprocess", function() {
                    $(config.currentTimeEl).text(formatTime(wavesurfer.getCurrentTime()));
                });

                wavesurfer.on("finish", function() {
                    $(config.playButton).html('<i class="fas fa-play"></i>');
                });

                // Load audio file
                wavesurfer.load(config.filePath);

                // Store active instance
                activeWavesurfers[config.id] = wavesurfer;

                return wavesurfer;
            }

            // Format time helper function
            function formatTime(seconds) {
                var minutes = Math.floor(seconds / 60);
                var seconds = Math.floor(seconds % 60);
                return (
                    (minutes < 10 ? "0" : "") +
                    minutes +
                    ":" +
                    (seconds < 10 ? "0" : "") +
                    seconds
                );
            }

            // Handle play button clicks with event delegation
            $(document).on('click', '[id^=play-button-]', function() {
                var id = $(this).attr("id").split("-")[2];
                var config = playerConfigs.find(p => p.id === id);

                if (!config) return;

                // Create instance if it doesn't exist
                if (!activeWavesurfers[id]) {
                    createWaveSurfer(config);
                }

                var wavesurfer = activeWavesurfers[id];

                // Resume AudioContext if suspended
                if (wavesurfer.backend && wavesurfer.backend.ac &&
                    wavesurfer.backend.ac.state === 'suspended') {
                    wavesurfer.backend.ac.resume();
                }

                // Pause all other players
                Object.keys(activeWavesurfers).forEach(function(key) {
                    if (key !== id) {
                        activeWavesurfers[key].pause();
                        $("#play-button-" + key).html('<i class="fas fa-play"></i>');
                    }
                });

                // Toggle play/pause
                if (wavesurfer.isPlaying()) {
                    wavesurfer.pause();
                    $(this).html('<i class="fas fa-play"></i>');
                } else {
                    wavesurfer.play();
                    $(this).html('<i class="fas fa-pause"></i>');
                }
            });

            // Handle tab switching
            $('.tab-button, [data-toggle="tab"]').on('click', function() {
                var targetTabId = $(this).data('target') || $(this).attr('href');
                if (targetTabId.startsWith('#')) {
                    targetTabId = targetTabId.substring(1);
                }

                // Pause all players
                Object.keys(activeWavesurfers).forEach(function(key) {
                    activeWavesurfers[key].pause();
                    $("#play-button-" + key).html('<i class="fas fa-play"></i>');
                });

                // Clear all existing wavesurfer instances
                Object.keys(activeWavesurfers).forEach(function(key) {
                    activeWavesurfers[key].destroy();
                    delete activeWavesurfers[key];
                });

                // Set a timeout to ensure the tab content is visible
                setTimeout(function() {
                    // Initialize players for visible waveforms in the current tab
                    $("[id^=waveform-]").each(function() {
                        var id = $(this).attr("id").split("-")[1];
                        var config = playerConfigs.find(p => p.id === id);

                        if (config && $(this).is(':visible')) {
                            createWaveSurfer(config);
                        }
                    });
                }, 300);
            });

            // Bootstrap tab event handler
            $(document).on('shown.bs.tab', function(e) {
                var targetTabId = $(e.target).attr('href');
                if (targetTabId && targetTabId.startsWith('#')) {
                    targetTabId = targetTabId.substring(1);
                }

                // Set a timeout to ensure the tab content is visible
                setTimeout(function() {
                    // Initialize players for visible waveforms in the current tab
                    $("[id^=waveform-]").each(function() {
                        var id = $(this).attr("id").split("-")[1];
                        var config = playerConfigs.find(p => p.id === id);

                        if (config && $(this).is(':visible')) {
                            createWaveSurfer(config);
                        }
                    });
                }, 300);
            });

            // Collect configurations on page load
            $(document).ready(function() {
                collectPlayerConfigs();

                // Initialize players for visible waveforms
                $("[id^=waveform-]").each(function() {
                    if ($(this).is(':visible')) {
                        var id = $(this).attr("id").split("-")[1];
                        var config = playerConfigs.find(p => p.id === id);

                        if (config) {
                            createWaveSurfer(config);
                        }
                    }
                });
            });

        })(jQuery);
    </script>
@endpush
