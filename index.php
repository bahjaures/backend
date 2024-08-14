<?php
// Connexion à la base de données
session_start();
include 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Artisanat Express</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: gray;
            color: #333;
            overflow-x: hidden;
        }

        header {
            background-color: #1f2937;
        }

        .hero-section {
            background-image: url('../image/artisan.jpg');
            background-size: cover;
            background-position: center;
            position: relative;
            padding: 100px 20px;
            color: white;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.7));
        }

        .hero-content {
            position: relative;
            z-index: 2;
            opacity: 1;
            /* Modification pour rendre l'élément visible */
        }

        .service-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.4s ease-in-out;
            overflow: hidden;
            opacity: 1;
            /* Modification pour rendre l'élément visible */
            transform: translateY(0);
            /* Modification pour le rendre visible */
        }

        .service-card:hover {
            transform: scale(1.05);
        }

        .service-image {
            transition: transform 0.4s ease-in-out;
            object-fit: cover;
        }

        .service-card:hover .service-image {
            transform: scale(1.2);
        }

        footer {
            background-color: #1f2937;
            color: white;
            padding: 20px 0;
            text-align: center;
        }

        .btn {
            background-color: #ff6600;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: bold;
            text-transform: uppercase;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            display: inline-block;
            margin-top: 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .btn:hover {
            background-color: #e55b00;
            box-shadow: 0px 6px 8px rgba(0, 0, 0, 0.15);
        }

        .btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 300%;
            height: 300%;
            background: rgba(255, 255, 255, 0.1);
            transform: translate(-50%, -50%) scale(0);
            border-radius: 50%;
            transition: transform 0.6s ease;
        }

        .btn:hover::after {
            transform: translate(-50%, -50%) scale(1);
        }
    </style>
</head>

<body>

    <header class="bg-white shadow fixed w-full z-50 top-0 left-0 py-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <img src="../image/logo.png" alt="Logo" class="h-12 mr-4 animate__animated animate__fadeInLeft">
                <h1 class="text-3xl font-bold text-orange-600">Artisanat Express</h1>
            </div>
            <nav class="space-x-4">
                <a href="register.php" class="btn animate__animated animate__fadeInDown">Inscription</a>
                <a href="login.php" class="btn animate__animated animate__fadeInDown">Connexion</a>
            </nav>
        </div>
    </header>

    <section class="hero-section">
        <div class="hero-content animate__animated animate__fadeInUp">
            <h2 class="text-5xl font-extrabold">Découvrez les <span class="text-orange-600">meilleurs artisans</span> près de chez vous !</h2>
            <p class="text-lg mt-4">Des services rapides et de qualité par des artisans qualifiés.</p>
            <a href="services.php" class="btn">Voir les services</a>
        </div>
    </section>

    <main class="container mx-auto mt-24">
        <section class="grid md:grid-cols-3 gap-12">
            <div class="service-card" data-scroll>
                <img src="../image/Design.png" alt="Services" class="service-image w-full h-48 object-cover rounded-lg mb-4">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Nos Services</h3>
                <p class="text-gray-600 mb-4">Découvrez les services offerts par nos artisans qualifiés.</p>
                <a href="services.php" class="btn">Voir les services <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="service-card" data-scroll>
                <img src="../image/article.webp" alt="Articles" class="service-image w-full h-48 object-cover rounded-lg mb-4">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Nos Articles</h3>
                <p class="text-gray-600 mb-4">Découvrez les articles proposés par nos artisans.</p>
                <a href="client_article.php" class="btn">Voir les articles <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="service-card" data-scroll>
                <img src="../image/livreur.jpg" alt="Livreurs" class="service-image w-full h-48 object-cover rounded-lg mb-4">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Nos Livreurs</h3>
                <p class="text-gray-600 mb-4">Contactez nos livreurs pour la livraison de vos commandes.</p>
                <a href="livreur.php" class="btn">Voir les livreurs <i class="fas fa-arrow-right"></i></a>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Artisanat Express. Tous droits réservés.</p>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ScrollReveal.js/4.0.9/scrollreveal.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            gsap.from('.hero-content', {
                duration: 1.5,
                opacity: 0,
                y: 50,
                ease: 'power4.out'
            });

            ScrollReveal().reveal('.service-card', {
                duration: 1000,
                distance: '50px',
                origin: 'bottom',
                opacity: 0,
                delay: 200,
                reset: true
            });
        });
    </script>
</body>

</html>