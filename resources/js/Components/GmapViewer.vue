<template>
  <div>
    <GmapMap
        :center="{lat:lat, lng:lng}"
        :zoom="14"
        :options="{
          zoomControl: true,
          mapTypeControl: false,
          scaleControl: false,
          streetViewControl: false,
          rotateControl: false,
          fullscreenControl: true,
          clickableIcons: false,
          disableDefaultUi: false,
        }"
        class="gmap"
    >
      <GmapMarker
          :position="{lat:lat, lng:lng}"
      ></GmapMarker>
    </GmapMap>
  </div>
</template>

<style scoped>
.gmap, iframe {
  width: 400px;
  height: 300px;
  max-width: 100%;
  max-height: 100vh;
  border: none;
}
</style>

<script>
export default {
  props: {
    lat: {
      required: true,
      type: Number,
    },
    lng: {
      required: true,
      type: Number,
    }
  }
}
</script>
