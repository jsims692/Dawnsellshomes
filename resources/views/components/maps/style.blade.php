{{-- The one brand basemap style shared by every Google map on the site
     (sales map, listings search, listing-detail location). Soft greys,
     muted POIs (parks stay green), navy-tinted water. Include once per
     page before any map script; the guard makes double-includes safe. --}}
<script>
window.dsMapStyle ||= [
  { elementType: 'geometry', stylers: [{ color: '#f4f6fb' }] },
  { elementType: 'labels.text.fill', stylers: [{ color: '#4a5568' }] },
  { elementType: 'labels.text.stroke', stylers: [{ color: '#f4f6fb' }] },
  { elementType: 'labels.icon', stylers: [{ visibility: 'off' }] },
  { featureType: 'administrative', elementType: 'geometry.stroke', stylers: [{ color: '#c9d1e0' }] },
  { featureType: 'administrative.locality', elementType: 'labels.text.fill', stylers: [{ color: '#0F1E2E' }, { weight: 0.5 }] },
  { featureType: 'administrative.neighborhood', stylers: [{ visibility: 'off' }] },
  { featureType: 'landscape.man_made', elementType: 'geometry', stylers: [{ color: '#f8f6f2' }] },
  { featureType: 'landscape.natural', elementType: 'geometry', stylers: [{ color: '#eef1f6' }] },
  { featureType: 'poi', stylers: [{ visibility: 'off' }] },
  { featureType: 'poi.park', elementType: 'geometry', stylers: [{ color: '#e3ebe0' }, { visibility: 'on' }] },
  { featureType: 'poi.park', elementType: 'labels.text.fill', stylers: [{ color: '#7a8f76' }] },
  { featureType: 'road', elementType: 'geometry', stylers: [{ color: '#ffffff' }] },
  { featureType: 'road', elementType: 'geometry.stroke', stylers: [{ color: '#e1e6ef' }] },
  { featureType: 'road', elementType: 'labels.text.fill', stylers: [{ color: '#7b8494' }] },
  { featureType: 'road.highway', elementType: 'geometry', stylers: [{ color: '#f3ead2' }] },
  { featureType: 'road.highway', elementType: 'geometry.stroke', stylers: [{ color: '#e6d6ab' }] },
  { featureType: 'road.highway', elementType: 'labels.text.fill', stylers: [{ color: '#8a7a4a' }] },
  { featureType: 'road.arterial', elementType: 'geometry', stylers: [{ color: '#ffffff' }] },
  { featureType: 'road.local', elementType: 'labels', stylers: [{ visibility: 'off' }] },
  { featureType: 'transit', stylers: [{ visibility: 'off' }] },
  { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#c5d5ea' }] },
  { featureType: 'water', elementType: 'labels.text.fill', stylers: [{ color: '#0F1E2E' }] },
];
</script>
