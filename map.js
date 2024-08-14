// map.js
document.addEventListener("DOMContentLoaded", function () {
  // Initialiser la carte
  var map = L.map("map").setView([5.354, -3.996], 13); // Coordonnées pour Abidjan, Côte d'Ivoire

  // Ajouter une couche de carte OpenStreetMap
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution:
      '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
  }).addTo(map);

  // Ajouter un marqueur
  var marker = L.marker([5.354, -3.996])
    .addTo(map)
    .bindPopup("<b>Abidjan</b><br>Côte d'Ivoire.")
    .openPopup();
});
