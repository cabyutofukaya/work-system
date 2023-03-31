<template>
  <div>
    <GmapMap
        :center="{lat:this.lat, lng:this.lng}"
        :zoom="18"
        :options="{
          zoomControl: true,
          mapTypeControl: false,
          scaleControl: false,
          streetViewControl: false,
          rotateControl: false,
          //fullscreenControl: true,
          fullscreenControl: false,
          disableDefaultUi: false,
          clickableIcons: false,
          gestureHandling: 'greedy'
        }"
        class="gmap"
        ref="mapRef"
    >
      <GmapMarker
          :position="{lat:this.newLat, lng:this.newLng}"
          :clickable="true"
          :draggable="true"
          @drag="updateCoordinates"
      ></GmapMarker>
    </GmapMap>

    <div class="menu">
      <Button @click.native="closeGmapPicker">この位置で決定</Button>
      <br>
      <Button @click.native="cancelGmapPicker">キャンセル</Button>
    </div>

    <transition name="fade">
      <div class="usage" v-if="usageShow" @mouseover="usageShow = false">
        マーカーピンの位置を動かして、位置を指定してください
      </div>
    </transition>
  </div>
</template>

<style scoped>
#app {
  height: 100vh;
}

.gmap, iframe {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  border: none;
  z-index: 100;
}

.menu {
  position: fixed;
  top: 10px;
  right: 10px;
  margin: 0 !important;
  z-index: 200;
  text-align: right;
}

.menu button {
  margin-bottom: 0.5em;
}

.usage {
  pointer-events: none;
  position: fixed;
  top: 50vh;
  left: 50vw;
  transform: translate(-50%, -50%);
  background-color: rgba(255, 255, 255, 0.8);
  z-index: 200;
  padding: 3em 1em;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity .5s;
}

.fade-enter, .fade-leave-to {
  opacity: 0;
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
    },
  },

  data() {
    return {
      usageShow: true,
      // 新しい現在位置
      newLat: Number,
      newLng: Number,
    };
  },

  created() {
    this.newLat = this.lat;
    this.newLng = this.lng;
  },

  mounted() {
    setTimeout(() => {
      this.usageShow = false
    }, 3000)
  },

  methods: {
    // ドラッグさられたら位置情報を更新
    updateCoordinates(location) {
      this.newLat = location.latLng.lat();
      this.newLng = location.latLng.lng();
    },

    closeGmapPicker() {
      // クローズボタンで位置情報を親コンポーネントに通知
      this.$emit('getGmapPickerCoordinates', {
        lat: this.newLat,
        lng: this.newLng,
      });

      // マップを閉じる
      this.$emit('closeGmapPicker');
    },

    cancelGmapPicker() {
      // マップを閉じる
      this.$emit('closeGmapPicker');
    }
  },
}
</script>
