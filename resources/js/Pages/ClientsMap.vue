<template>
  <div>
    <GmapMap
        :center="{lat:this.center.lat, lng:this.center.lng}"
        :zoom="14"
        :options="{
                    zoomControl: true,
                    mapTypeControl: false,
                    scaleControl: false,
                    streetViewControl: false,
                    rotateControl: false,
                    fullscreenControl: false,
                    disableDefaultUi: false,
                    clickableIcons: false,
                    gestureHandling: 'greedy'
                 }"
        class="gmap"
        ref="mapRef"
        @click="infoWinOpen=false"
    >
      <GmapInfoWindow
          :options="infoOptions"
          :position="infoWindowPos"
          :opened="infoWinOpen"
          @closeclick="infoWinOpen=false"
      >
        <div class="info">
          <div>
            <Link v-if="infoContent.id" :href="$route('clients.show', {client: infoContent.id})">
              <v-avatar tile>
                <v-img :src="infoContent.icon_image_url" alt=""></v-img>
              </v-avatar>
            </Link>
          </div>

          <div>
            <Link v-if="infoContent.id" :href="$route('clients.show', {client: infoContent.id})">
              {{ infoContent.name }}
            </Link>
            <br>
            {{ infoContent.address }}
          </div>
        </div>
      </GmapInfoWindow>

      <GmapMarker
          :key="i"
          v-for="(m,i) in markers"
          :position="m.position"
          :clickable="true"
          :draggable="false"
          :title="m.name"
          @click="toggleInfoWindow(m,i)"
      ></GmapMarker>
    </GmapMap>

    <div id="menu">
      <v-text-field
          dense filled
          class="ma-0 pa-0 d-inline"
          append-icon="mdi-magnify"
          label="所在地から検索"
          hint="入力した所在地・郵便番号の位置に地図の中心を移動します" persistent-hint
          v-model="address"
          @click:append="moveFromAddress"
          @keydown.enter="moveFromAddress"
          :autofocus="!$vuetify.breakpoint.xs"
          ref="address"
      ></v-text-field>

      <back-button
          style="margin-top:1em"
          :backTo="{name:'Customers'}"
      ></back-button>
    </div>
  </div>
</template>

<style scoped>
/*フルスクリーン*/
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

/* インフォウィンドウ */
.info {
  padding-right: 12px;
  padding-bottom: 12px;
  display: flex;
  column-gap: 6px;
}

/* ウィンドウ内の最初の要素に自動で設定されるフォーカスのアウトラインを削除 */
.info a:focus-within {
  outline: none;
}

/* ボタンエリア **/
#menu {
  position: fixed;
  top: 10px;
  right: 10px;
  max-width: calc(100vw - 20px);
  margin: 0 !important;
  z-index: 200;
  text-align: right;
}

/*#menu div {*/
/*  max-width: 80vw;*/
/*}*/
</style>

<style>
/** v-input-fieldの背景色を変更 **/
#menu .v-input__control {
  background: #fff !important;
}
</style>

<script>
import Layout from "./Layout";
import {Link} from "@inertiajs/inertia-vue";
import {gmapApi} from "gmap-vue";

export default {
  components: {Layout, Link},

  props: ['client_type', 'markers', 'location_default'],

  data() {
    return {
      // 現在位置の初期値
      latitude: this.location_default["lat"],
      longitude: this.location_default["lng"],

      center: {
        lat: this.location_default["lat"],
        lng: this.location_default["lng"]
      },

      // 所在地検索ワード
      address: "",

      // 会社情報ウィンドウ
      infoWindowPos: null,
      infoWinOpen: false,
      currentMidx: null,

      // ウィンドウ表示時に代入する会社情報コンテンツ
      infoContent: {
        id: null,
        name: null,
        address: null,
        icon_image_url: null
      },

      // 情報ウィンドウオプション
      infoOptions: {
        pixelOffset: {
          // width: 0,
          // height: -35
        }
      },
    };
  },

  computed: {
    google: gmapApi, // gmap-vueでAPIへの直接のアクセスを可能にする
  },

  mounted() {
    // マーカーがなければ警告
    if (!this.markers.length) {
      alert('まだ会社情報がありません');
      window.history.back();
      return;
    }

    // すべてのマーカーを含むように画面領域を移動
    this.$gmapApiPromiseLazy().then(() => {
      this.moveAllMarkers();
    });

    // 現在位置を取得 (未使用)
    //this.getLocation();
  },

  methods: {
    // マーカー選択で情報ウィンドウを開く
    toggleInfoWindow: function (marker) {
      this.infoWinOpen = false;
      this.infoWindowPos = marker.position;
      this.infoContent = marker.content;
      this.infoWinOpen = true;
    },

    // すべてのマーカーを含むように画面領域を移動
    moveAllMarkers() {
      if (this.markers.length) {
        const bounds = new this.google.maps.LatLngBounds();

        for (let marker of this.markers) {
          bounds.extend({"lat": marker.position.lat, "lng": marker.position.lng})
        }

        this.$refs.mapRef.fitBounds(bounds);
      }
    },

    // ジオコーディングAPIにより住所から位置情報を取得し移動する
    moveFromAddress() {
      if (this.google && this.address) {
        new this.google.maps.Geocoder().geocode(
            {address: this.address},
            (results, status) => {
              if (status === this.google.maps.GeocoderStatus.OK) {
                const location = results[0].geometry.location;
                // 地図の中心を移動
                this.center.lat = location.lat();
                this.center.lng = location.lng();

                // 地図を住所範囲にフィット
                const bounds = new this.google.maps.LatLngBounds(
                    results[0].geometry.viewport.getSouthWest(),
                    results[0].geometry.viewport.getNorthEast()
                );
                this.$refs.mapRef.fitBounds(bounds);

                this.$toasted.success('"' + results[0].formatted_address + '"に移動しました');
              } else {
                this.$toasted.error('所在地から位置を設定できませんでした');
              }
            }
        );
      } else {
        // すべてのマーカーを含むように画面領域を移動
        this.moveAllMarkers();
      }

      // モバイルでIMEが再表示されることを防ぐためテキストエリアからフォーカスを外す
      this.$refs.address.blur();
    },

    // 現在位置を取得 (未使用)
    getLocation() {
      if (!navigator.geolocation) {
        alert('現在地情報を取得できませんでした。お使いの環境では現在地情報を利用できない可能性があります。');
        return;
      }

      const options = {
        enableHighAccuracy: false,
        timeout: 5000,
        maximumAge: 0
      };

      navigator.geolocation.getCurrentPosition(this.success, this.error, options);
    },

    // 現在位置 取得成功
    success(position) {
      this.latitude = position.coords.latitude;
      this.longitude = position.coords.longitude;
    },

    // 現在位置 取得失敗
    error(error) {
      switch (error.code) {
        case 1: //PERMISSION_DENIED
          alert('位置情報の利用が許可されていません。');
          break;
        case 2: //POSITION_UNAVAILABLE
          alert('現在位置が取得できませんでした。');
          break;
        case 3: //TIMEOUT
          alert('タイムアウトしました。');
          break;
        default:
          alert('現在位置が取得できませんでした。');
          break
      }
    }
  }
}
</script>
