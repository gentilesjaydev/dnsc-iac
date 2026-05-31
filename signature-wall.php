<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signature Wall - IAC Covenant</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/images/iclogo.png" type="image/x-icon">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Outfit Font -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --accent-purple: #7c3aed;
            --text-dark: #2e1065;
        }

        body {
            background-color: var(--wall-bg);
            color: var(--text-dark);
            font-family: 'Outfit', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            /* Subgrid texture to look like a wall/whiteboard */
            background-image:
                linear-gradient(rgba(124, 58, 237, .03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(124, 58, 237, .03) 1px, transparent 1px);
            background-size: 50px 50px;
        }

        .wall-header {
            padding: 1.5rem 2rem;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            border-bottom: 2px solid #ddd;
            position: relative;
            z-index: 1000;
        }

        .header-container {
            max-width: 1600px;
            margin: 0 auto;
        }

        .header-logo {
            height: 100px;
            width: auto;
            object-fit: contain;
        }

        .header-text-content {
            flex: 1;
            text-align: center;
            padding: 0 2rem;
        }

        .main-event-title {
            font-family: 'Outfit';
            font-weight: 700;
            font-size: 1.4rem;
            color: #1e293b;
            margin-bottom: 0.3rem;
            line-height: 1.2;
        }

        .event-details {
            font-family: 'Outfit';
            color: #475569;
            font-size: 1.1rem;
            font-weight: 500;
        }

        .event-details p {
            margin: 0;
            line-height: 1.4;
        }

        .wall-watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: -1;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            user-select: none;
            opacity: 0.1;
            filter: blur(4px);
            text-align: center;
        }

        .watermark-text {
            font-family: 'Outfit';
            font-weight: 900;
            font-size: clamp(5rem, 15vw, 12rem);
            color: var(--text-dark);
            line-height: 0.8;
            margin: 0;
            letter-spacing: -5px;
        }

        .watermark-logos {
            display: flex;
            gap: 3rem;
            margin-top: 2rem;
        }

        .watermark-logos img {
            height: 150px;
            object-fit: contain;
        }

        .signature-container {
            position: relative;
            padding: 4rem 2rem;
            min-height: calc(100vh - 150px);
            width: 100%;
            max-width: 1600px;
            margin: 0 auto;
            overflow: visible;
        }

        .signature-item {
            position: absolute;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            z-index: 1;
            user-select: none;
            -webkit-user-drag: none;
            will-change: transform, top, left;
            /* Use variables for the animation to target */
            transform: rotate(var(--rot)) scale(var(--scale));
        }

        .signature-item:hover {
            transform: rotate(var(--rot)) scale(calc(var(--scale) * 1.2)) !important;
            z-index: 50;
            filter: drop-shadow(0 15px 30px rgba(0, 0, 0, 0.15));
        }

        .signature-img {
            max-width: 240px;
            height: auto;
            max-height: 140px;
            object-fit: contain;
            mix-blend-mode: multiply;
        }

        /* POP-IN ANIMATION */
        @keyframes popSignature {
            0% {
                opacity: 0;
                transform: rotate(var(--rot)) scale(0);
            }

            70% {
                transform: rotate(var(--rot)) scale(calc(var(--scale) * 1.2));
            }

            100% {
                opacity: 1;
                transform: rotate(var(--rot)) scale(var(--scale));
            }
        }

        .new-sig {
            animation: popSignature 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        /* INDICATOR FOR LATEST SIGNING */
        .latest-indicator {
            position: absolute;
            top: -25px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--accent-purple);
            color: white;
            font-size: 0.65rem;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 10px;
            white-space: nowrap;
            pointer-events: none;
            box-shadow: 0 4px 10px rgba(124, 58, 237, 0.3);
            z-index: 10;
        }

        .fullscreen-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: var(--accent-purple);
            color: white;
            border: none;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 5px 20px rgba(124, 58, 237, 0.4);
            z-index: 1001;
            transition: all 0.3s;
            font-size: 1.5rem;
        }

        .fullscreen-btn:hover {
            transform: scale(1.1);
            background: #0284c7;
        }

        @media (max-width: 768px) {
            .wall-header {
                padding: 1rem 0.5rem;
            }

            .header-container {
                flex-direction: column !important;
                gap: 1rem;
            }

            .header-logo {
                height: 65px;
            }

            .header-text-content {
                padding: 0;
            }

            .main-event-title {
                font-size: 1rem;
            }

            .event-details {
                font-size: 0.85rem;
            }

            .signature-container {
                padding: 2rem 1rem;
            }

            .signature-img {
                max-width: 140px;
            }

            .watermark-text {
                font-size: 5rem;
            }
        }
    </style>
</head>

<body>

    <div class="wall-watermark">
        <h1 class="watermark-text">IAC</h1>
        <h1 class="watermark-text" style="color: var(--accent-purple);">Covenant</h1>
        <div class="watermark-logos">
            <img src="assets/images/dnsclogo.png" alt="" onerror="this.style.display='none'">
            <img src="assets/images/iclogo.png" alt="" style="height: 180px;" onerror="this.style.display='none'">
        </div>
    </div>

    <header class="wall-header">
        <div class="header-container d-flex align-items-center justify-content-between">
            <img src="assets/images/dnsclogo.png" alt="DNSC" class="header-logo" onerror="this.style.display='none'">

            <div class="header-text-content">
                <h1 class="main-event-title" style="color: var(--accent-purple); font-size: 1.8rem;">IAC Covenant</h1>
                <h2 class="main-event-title" style="font-size: 1.1rem; color: #475569;">DNSC INSTITUTE OF COMPUTING INDUSTRY ADVISORY COUNCIL MEETING through SPRINT-IT</h2>
                <div class="event-details">
                    <p>Venue: DNSC GAD Conference Room and Via MS Teams</p>
                    <p>Date: May 4, 2026 8:00-1:00 PM</p>
                </div>
            </div>

            <img src="assets/images/iclogo.png" alt="IC" class="header-logo" style="height: 120px;" onerror="this.style.display='none'">
        </div>
    </header>

    <main>
        <div id="signaturesContainer" class="signature-container">
            <!-- Signatures will be placed here -->
        </div>
    </main>

    <button id="fullscreenToggle" class="fullscreen-btn" title="Toggle Fullscreen">
        <i class="fa-solid fa-expand"></i>
    </button>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        let signatureIds = new Set();

        function getRandomRotation() {
            // Random degrees between -12 and 12
            return (Math.random() * 24 - 12).toFixed(1);
        }

        function getRandomScale() {
            // Random scale between 0.85 and 1.15
            return (0.85 + Math.random() * 0.3).toFixed(2);
        }

        let isFetching = false;
        function fetchSignatures() {
            if (isFetching) return;
            isFetching = true;

            $.ajax({
                url: 'api/fetch_signatures.php',
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        const container = $('#signaturesContainer');

                        // First load check
                        const isFirstLoad = signatureIds.size === 0;
                        if (isFirstLoad) {
                            container.empty();
                        }

                        // Process signatures
                        response.data.forEach((sig) => {
                            if (!signatureIds.has(sig.id)) {
                                signatureIds.add(sig.id);

                                const left = sig.pos_left;
                                const top = sig.pos_top;
                                const rotation = sig.pos_rotation;
                                const scale = sig.pos_scale;

                                const item = $(`
                                    <div class="signature-item ${!isFirstLoad ? 'new-sig' : ''}" 
                                         style="left: ${left}%; top: ${top}%; --rot: ${rotation}deg; --scale: ${scale};">
                                        ${!isFirstLoad ? '<div class="latest-indicator">JUST SIGNED!</div>' : ''}
                                        <img src="assets/signatures/${sig.signature_file}" alt="" class="signature-img" onerror="$(this).parent().hide();">
                                    </div>
                                `);

                                // Append to the container
                                container.append(item);

                                // If it's a new signature (not initial load), show the indicator and fade it out
                                if (!isFirstLoad) {
                                    setTimeout(() => {
                                        item.find('.latest-indicator').fadeOut(1000, function () {
                                            $(this).remove();
                                        });
                                    }, 5000);
                                }
                            }
                        });
                    }
                },
                error: function (err) {
                    console.error("Failed to fetch signatures", err);
                },
                complete: function() {
                    isFetching = false;
                }
            });
        }

        // Fullscreen toggle logic
        $('#fullscreenToggle').on('click', function () {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
                $(this).html('<i class="fa-solid fa-compress"></i>');
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                    $(this).html('<i class="fa-solid fa-expand"></i>');
                }
            }
        });

        // Initial fetch
        fetchSignatures();

        // Auto refresh every 10 seconds to show new signings live
        setInterval(fetchSignatures, 10000);
    </script>
</body>

</html>




