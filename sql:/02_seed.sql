-- EcoRide seed data (extrait du dump phpMyAdmin)
USE `ecoride_db`;

START TRANSACTION;

SET FOREIGN_KEY_CHECKS=0;
INSERT INTO `users` (`id`, `pseudo`, `email`, `password_hash`, `credits`, `created_at`) VALUES
(1, 'ecoride', 'contact@ecoride.fr', '$2y$10$yDCXWGTUrelR02VpNJlcBuprTNV5Cwg6HHZRjVNhOAgre.pc0kvrq', 20, '2025-12-14 08:16:29'),
(2, 'testmoussa', 'testmoussa@mail.com', '$2y$10$uKyFZIVrOoV6C.JDY6RQHenkKBAutEd/OxkEZ7BJjgKlXMAnjJrFq', 20, '2026-02-22 00:03:18');

INSERT INTO `rides` (`id`, `driver_id`, `depart`, `arrivee`, `date_ride`, `heure_depart`, `heure_arrivee`, `prix`, `places_restantes`, `ecologique`, `created_at`) VALUES
(1, 1, 'Paris', 'Lyon', '2025-12-20', '08:00:00', '12:00:00', 25.00, 2, 1, '2025-12-14 20:26:36'),
(2, 1, 'Marseille', 'Nice', '2025-12-22', '09:30:00', '11:30:00', 18.00, 1, 0, '2025-12-14 20:26:36'),
(3, 1, 'Toulouse', 'Bordeaux', '2025-12-24', '07:00:00', '10:00:00', 22.00, 3, 1, '2025-12-14 20:26:36'),
(4, 2, 'paris', 'lyon', '2026-02-23', '10:00:00', '14:00:00', 20.00, 3, 1, '2026-02-22 00:22:26');

INSERT INTO `bookings` (`id`, `user_id`, `ride_id`, `created_at`, `status`, `cancelled_at`) VALUES
(1, 1, 2, '2025-12-14 20:42:06', 'cancelled', '2026-02-14 11:42:56'),
(2, 2, 1, '2026-02-22 07:00:18', 'cancelled', '2026-02-22 07:05:09');

SET FOREIGN_KEY_CHECKS=1;
COMMIT;
