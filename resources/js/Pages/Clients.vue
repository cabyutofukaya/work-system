<template>
  <Layout>
    <v-card flat tile>
      <v-card-title>
        <v-icon dark left v-html="client_type.icon"></v-icon>
        {{ client_type.name }}
      </v-card-title>

      <v-card-text>
        <v-row>
          <v-col>
            <Link as="Button" :small="$vuetify.breakpoint.xs" @click.native="searchDialog = true"
              :color="formParamsCount ? 'warning' : undefined">
            <v-icon left>
              mdi-filter-outline
            </v-icon>
            絞り込み<span v-if="formParamsCount">({{ formParamsCount }})</span>
            </Link>
          </v-col>

          <v-col class="text-right">
            <Link as="Button" :class="{ 'mb-2': $vuetify.breakpoint.xs }" :small="$vuetify.breakpoint.xs"
              :to="$route('client-types.clients.create', { client_type: client_type.id })">
            <v-icon left>
              mdi-plus
            </v-icon>
            会社を作成する
            </Link>
          </v-col>
        </v-row>

      </v-card-text>

      <v-list>
        <div v-for="client in clients['data']" :key="client.id">
          <Link as="v-list-item" three-line :href="$route('clients.show', { client: client.id })" exact
            style="position: relative;">
          <!-- <Link as="v-list-item" three-line  exact 
            style="position: relative;" @click="(event) => clickAlert(client.id)"> -->
          <v-list-item-avatar tile>
            <v-img :src="client['icon_image_url']" alt=""></v-img>
          </v-list-item-avatar>

          <v-list-item-content>
            <v-list-item-title class="font-weight-bold mb-1">

              <template v-if="client.name_position == '前' || client.name_position == '後ろ'">
                <template v-if="client.name_position == '前'">
                  {{ client.type_name }} {{ client.name }}
                </template>

                <template v-if="client.name_position == '後ろ'">
                  {{ client.name }} {{ client.type_name }}
                </template>

              </template>

              <template v-else>
                {{ client.name }}
              </template>


              <template v-if="client.business_name">
                / {{ client.business_name }}
              </template>


            </v-list-item-title>

            <v-list-item-subtitle>
              <span style="white-space: pre-line;" v-if="client.description">{{ client.description }} / </span>
              <span style="white-space: pre-line;font-size: small;" v-if="client.prefecture"> 所在地:</span>
              <span style="white-space: pre-line;" v-if="client.prefecture"> {{ client.prefecture }}{{ client.address }}</span>
              <span style="white-space: pre-line;font-size: small;" v-if="client.tel">電話番号:</span>
              <span style="white-space: pre-line;color: #2d9acd;text-decoration: underline;" v-if="client.tel"
                @click="(event) => clickAlert(client.tel, event)">{{ client.tel }}</span>
              <!-- <span style="white-space: pre-line;" v-if="client.tel"@click="(event) => clickAlert(client.tel, event)"><object><a :href="'tel:' + client.tel"
                  >{{ client.tel }}</a></object></span> -->
            </v-list-item-subtitle>
          </v-list-item-content>
          </Link>

          <v-divider class="mx-4"></v-divider>
        </div>
      </v-list>

      <v-card-text v-if="!clients['total']">
        条件に一致する会社は見つかりませんでした
      </v-card-text>

      <v-card-text>
        <v-pagination v-model="page" :length="length" @input="changePage"></v-pagination>
      </v-card-text>

      <v-divider></v-divider>

      <v-card-text class="text-right">
        <BackButton></BackButton>
      </v-card-text>
    </v-card>

    <!-- 絞り込み条件ダイアログ -->
    <v-dialog v-model="searchDialog" :max-width="$vuetify.breakpoint.smAndUp ? '600px' : 'unset'"
      @click:outside="closeSearchDialog">
      <v-card flat tile>
        <v-card-title>
          <template v-if="initSearch">
            {{ client_type.name }}を探す
          </template>
          <template v-else>
            絞り込み
          </template>
        </v-card-title>

        <form @submit.prevent="search">
          <v-card-text :class="{ 'pt-0': Boolean($vuetify.breakpoint.xs) }">
            <v-list>
              <v-list-item v-if="initSearch">
                <v-row>
                  <v-col cols="12" sm="4" class="text-center" :class="{ 'pb-0': Boolean($vuetify.breakpoint.xs) }">
                    <Button block :to="$route('client-types.clients.map', { client_type: this.client_type.id })">
                      <v-icon left>
                        mdi-map-outline
                      </v-icon>
                      地図から探す
                    </Button>
                  </v-col>

                  <v-col cols="12" sm="4" class="text-center" v-if="!$vuetify.breakpoint.xs">
                    <Button block type="submit" :loading="loading['search']">
                      <v-icon left>
                        mdi-magnify
                      </v-icon>
                      この条件で検索
                    </Button>
                  </v-col>

                  <v-col cols="12" sm="4" class="text-center">
                    <Button block :to="$route('client-types.clients.create', { client_type: client_type.id })">
                      <v-icon left>
                        mdi-plus
                      </v-icon>
                      新規作成
                    </Button>
                  </v-col>
                </v-row>
              </v-list-item>

              <v-divider v-if="initSearch && Boolean($vuetify.breakpoint.xs)" class="mt-4"></v-divider>

              <v-list-item class="mt-4">
                <v-text-field dense filled clearable prepend-inner-icon="mdi-pencil" name="word" label="ワード検索"
                  hint="会社名(ふりがな)・電話番号から指定したワードを含む項目に絞り込みます" persistent-hint maxlength="200" v-model="form['word']"
                  :autofocus="!$vuetify.breakpoint.xs"></v-text-field>
              </v-list-item>

              <v-list-item>
                <v-text-field dense filled clearable prepend-inner-icon="mdi-pencil" name="address" label="所在地検索"
                  hint="本社登録をした所在地に指定したワードを含む項目に絞り込みます" persistent-hint maxlength="200"
                  v-model="form['address']"></v-text-field>
              </v-list-item>

              <v-list-item>
                <v-text-field dense filled clearable prepend-inner-icon="mdi-pencil" name="branch" label="営業所検索"
                  hint="営業所に指定したワードを含む項目に絞り込みます" persistent-hint maxlength="200"
                  v-model="form['branch']"></v-text-field>
              </v-list-item>

              <v-list-item v-if="client_type.id === 'taxibus'">
                <v-text-field dense filled clearable prepend-inner-icon="mdi-pencil" name="business_district"
                  label="営業エリア検索" hint="営業エリアに指定したワードを含む項目に絞り込みます" persistent-hint maxlength="200"
                  v-model="form['business_district']"></v-text-field>
              </v-list-item>

              <v-list-item v-if="client_type['categories']">
                <v-select dense filled clearable name="category" label="カテゴリー" hint="指定したカテゴリーの項目に絞り込みます"
                  persistent-hint v-model="form.category"
                  :items="Object.entries(client_type['categories']).map(([value, text]) => ({ value, text }))"></v-select>
              </v-list-item>

              <v-list-item>
                <v-select dense filled clearable name="genre_id" label="ジャンル" hint="指定したジャンルの項目に絞り込みます" persistent-hint
                  maxlength="200" v-model="form.genre_id" :items="genres" item-value="id" item-text="name"></v-select>
              </v-list-item>

              <v-list-item v-if="client_type.id === 'taxibus'">
                <v-text-field dense filled clearable prepend-inner-icon="mdi-pencil" name="vehicle" label="保有車両検索"
                  hint="保有車両名・説明文からワードを含む項目に絞り込みます" persistent-hint maxlength="200"
                  v-model="form['vehicle']"></v-text-field>
              </v-list-item>


              <v-list-item>
                <v-select dense filled clearable name="product" label="商材" hint="指定した商材に関して日報が存在する企業を絞り込みます" persistent-hint
                  maxlength="200" v-model="form.product_id" :items="products" item-value="id" item-text="name"></v-select>
              </v-list-item>

              <!-- スイッチ -->
              <template v-if="client_type.id === 'taxibus'">
                <v-list-item class="mt-4">
                  <v-row class="my-0">
                    <template v-for="key in ['has_dr_sightseeing', 'has_dr_female']">
                      <v-col cols="12" sm="6" class="py-0">
                        <v-switch dense class="ma-0 pa-0" v-model="form[key]"
                          :label="client_type_column_names[key]"></v-switch>
                      </v-col>
                    </template>

                    <template
                      v-for="key in ['has_dr_language_english', 'has_dr_language_chinese', 'has_dr_language_korean', 'has_dr_language_other']">
                      <v-col cols="12" sm="6" class="py-0">
                        <v-switch dense class="ma-0 pa-0" v-model="form[key]"
                          :label="client_type_column_names[key].replace('外国語DR ', '') + 'DR'"></v-switch>
                      </v-col>
                    </template>

                    <v-col cols="12" sm="6" class="py-0">
                      <v-switch dense class="ma-0 pa-0" v-model="form['has_wheelchair']" label="車椅子"></v-switch>
                    </v-col>

                    <v-col cols="12" sm="6" class="py-0">
                      <v-switch dense class="ma-0 pa-0" v-model="form['has_baby_seat']" label="ベビーシート"></v-switch>
                    </v-col>

                    <v-col cols="12" sm="6" class="py-0">
                      <v-switch dense class="ma-0 pa-0" v-model="form['has_child_seat']" label="チャイルドシート"></v-switch>
                    </v-col>

                    <v-col cols="12" sm="6" class="py-0">
                      <v-switch dense class="ma-0 pa-0" v-model="form['has_junior_seat']" label="ジュニアシート"></v-switch>
                    </v-col>

                    <template v-for="key in ['is_bus_association_member', 'has_safety_mark']">
                      <v-col cols="12" sm="6" class="py-0">
                        <v-switch dense class="ma-0 pa-0" v-model="form[key]"
                          :label="client_type_column_names[key]"></v-switch>
                      </v-col>
                    </template>
                  </v-row>
                </v-list-item>
              </template>
            </v-list>
          </v-card-text>

          <v-card-text class="text-center pt-0">
            <Button type="submit" :loading="loading['search']">
              <v-icon left>
                mdi-magnify
              </v-icon>
              この条件で検索
            </Button>
          </v-card-text>
        </form>

        <v-divider></v-divider>

        <v-card-text class="text-right">
          <Button class="mt-4" @click.native="searchDialog = false">
            <v-icon>
              mdi-close
            </v-icon>
            閉じる
          </Button>
        </v-card-text>
      </v-card>
    </v-dialog>
  </Layout>
</template>

<script>
import Layout from "./Layout";
import { Link } from "@inertiajs/inertia-vue";

export default {
  components: { Layout, Link },

  props: ['client_type', 'client_type_column_names', 'genres', 'clients', 'form_params','products'],

  data() {
    return {
      // ページ情報の初期値
      page: Number(this.clients['current_page']),
      length: Number(this.clients['last_page']),

      // 初期検索モード メインメニューからの遷移時にデフォルトで検索ダイアログを開く
      initSearch: this.$route().params.initSearch ?? false,

      // 検索ダイアログ
      searchDialog: Boolean(this.$route().params.initSearch),
      form: this.$inertia.form(this.form_params),
      formParamsCount: Object
        .entries(this.form_params)
        .filter(function (param) {
          return param[1] && param[1].length !== 0;
        }).length,

      loading: {},
    };
  },

  methods: {
    // ページ移動
    changePage() {
      // サーバ側で生成された検索パラメータを含む最終ページURLを取得
      let url = new URL(this.clients["last_page_url"]);

      // ページ数の書き換え
      if (this.page !== 1) {
        url.searchParams.set('page', String(this.page));
      } else {
        url.searchParams.delete('page');
      }

      // ページ移動
      this.$inertia.get(url.href);
    },

    // 絞り込み実行
    search() {
      this.form
        .transform((data) => {
          // 値が空またはfalseの要素を削除
          Object.entries(data).forEach((kv) => {
            if (kv[1] === "" || kv[1] === null || kv[1] === false) {
              delete data[kv[0]];
            }
          });

          return { ...data };
        })
        .get(this.$route('client-types.clients.index', { client_type: this.client_type }), {
          onStart: () => this.$set(this.loading, "search", true),
          onSuccess: () => this.closeSearchDialog(),
          onFinish: () => this.$set(this.loading, "search", false),
        });
    },

    // ダイアログを閉じる
    closeSearchDialog() {
      //  ダイアログを閉じる
      this.searchDialog = false;

      //  初期検索モードを解除
      this.initSearch = false;
    },

    clickAlert(tel, e) {
      //伝播をストップ
      e.stopPropagation();
      e.preventDefault();

      // document.write(tel);
      // alert(tel);


      window.location.href = 'tel:' + tel;


    }
  }
}
</script>
