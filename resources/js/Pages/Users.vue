<template>
  <Layout>
    <v-card flat tile>
      <v-card-title>
        <v-icon dark left>mdi-account</v-icon>
        メンバー
      </v-card-title>

      <v-card-text>

        <v-row v-if="!$vuetify.breakpoint.xs">

          <v-col>
            <Link as="Button" :small="$vuetify.breakpoint.xs" @click.native="searchDialog = true"
              :color="formParamsCount ? 'warning' : undefined">
            <v-icon left>
              mdi-filter-outline
            </v-icon>
            絞り込み
            <!-- <span v-if="formParamsCount">({{ formParamsCount }})</span> -->
            </Link>
          </v-col>


     

          <v-col class="text-right">

            <Button :small="$vuetify.breakpoint.xs" :to="$route('user-profile-information.edit')">
              <v-icon left>
                mdi-pencil
              </v-icon>
              自分のメンバー情報を編集する
            </Button>
          </v-col>
        </v-row>

        <v-row v-if="$vuetify.breakpoint.xs">

          <v-col class="text-right">

            <Link as="Button" :small="$vuetify.breakpoint.xs" @click.native="searchDialog = true"
              :color="formParamsCount ? 'warning' : undefined">
            <v-icon left>
              mdi-filter-outline
            </v-icon>
            絞り込み
            <!-- <span v-if="formParamsCount">({{ formParamsCount }})</span> -->
            </Link>

            <Button :small="$vuetify.breakpoint.xs" :to="$route('user-profile-information.edit')">
              <v-icon left>
                mdi-pencil
              </v-icon>
              自分のメンバー情報を編集する
            </Button>
          </v-col>
        </v-row>

      </v-card-text>


      <!-- 絞り込み条件ダイアログ -->
      <v-dialog v-model="searchDialog" :max-width="$vuetify.breakpoint.smAndUp ? '600px' : 'unset'">
        <v-card flat tile>
          <v-card-title>
            絞り込み
          </v-card-title>

          <form @submit.prevent="search">
            <v-card-text>
              <v-list>

                <v-list-item>
                  <v-text-field dense filled clearable prepend-inner-icon="mdi-pencil" label="ワード検索"
                    hint="氏名・所属のから指定したワードを含む項目に絞り込みます。" persistent-hint name="word" v-model="form.word"
                    :error="Boolean(form.errors.word)" :error-messages="form.errors.word"
                    :autofocus="!$vuetify.breakpoint.xs"></v-text-field>
                </v-list-item>


                <v-list-item class="mt-10">
                  <v-select dense filled clearable prepend-inner-icon="mdi-pencil" label="担当商材" :items="productList"
                    return-object hint="担当商材の項目を絞り込みます。" persistent-hint name="product" v-model="form.product"
                    :error="Boolean(form.errors.product)" :error-messages="form.errors.product"
                    :autofocus="!$vuetify.breakpoint.xs"></v-select>
                </v-list-item>

              </v-list>
            </v-card-text>

            <v-card-text class="text-center">
              <Button :small="$vuetify.breakpoint.xs" type="submit">

                <v-icon left>
                  mdi-magnify
                </v-icon>
                この条件で検索する
              </Button>
            </v-card-text>
          </form>

          <v-divider></v-divider>

          <v-card-text class="text-right">
            <Button class="mt-4" :small="$vuetify.breakpoint.xs" @click.native="searchDialog = false">
              <v-icon>
                mdi-close
              </v-icon>
              閉じる
            </Button>
          </v-card-text>
        </v-card>
      </v-dialog>



      <v-row v-if="!$vuetify.breakpoint.xs">

        <v-col v-for="user in users['data']" :key="user.id" cols="2">

          <v-container fill-height>
            <v-card>


              <v-img v-bind:src="`/storage/user/${user.profile_img_file}`" class="grey lighten-2" max-height="150px"
                min-height="100px" v-if="user.profile_img_file" @click.native="clickImage(user.profile_img_file)">
                <template v-slot:placeholder>
                  <v-row class="ma-0" align="center" justify="center">
                    <v-progress-circular indeterminate></v-progress-circular>
                  </v-row>
                </template>
              </v-img>



              <v-img v-bind:src="`/storage/user/noimg.jpeg`" class="grey lighten-2" v-if="!user.profile_img_file"
                max-height="150px" min-height="100px">
                <template v-slot:placeholder>
                  <v-row class="ma-0" align="center" justify="center">
                    <v-progress-circular indeterminate></v-progress-circular>
                  </v-row>
                </template>
              </v-img>


              <v-card-text style="font-size: 16px;font-weight: bold;">
                <a :href="$route('users.show', { user: user.id })" dusk="userShow">
                  {{ user['name'] }}
                </a>
              </v-card-text>


              <v-card-text style="font-size: 12px;margin-top: -20px;">
                <v-icon>mdi-email-outline</v-icon> {{ user['email'] }} <br>
                <v-icon>mdi-account-group</v-icon> {{ user['department'] }}
              </v-card-text>

            </v-card>
          </v-container>

        </v-col>

      </v-row>

      <!-- </div> -->

      <v-list v-if="$vuetify.breakpoint.xs">
        
        <div v-for="user in users['data']" :key="user.id">
          <v-divider class="mx-4"></v-divider>

          <Link as="v-list-item" dusk="userShow" class="my-2">

          <v-row>

            <v-col cols="3">
              <img :src="`/storage/user/${user.profile_img_file}`" alt="" class="c-img" height="80px"
                v-if="user.profile_img_file" style="max-width: 80px;">

              <img :src="`/storage/user/noimg.jpeg`" alt="" class="c-img" height="80px" v-if="!user.profile_img_file">

            </v-col>

            <v-col cols="9">
              <v-list-item-title style="font-weight: bold;">
                {{ user["name"] }} <span v-if="user.name_kana">({{ user["name_kana"] }})</span>
              </v-list-item-title>

              <v-list-item-subtitle>
                <v-icon>mdi-email-outline</v-icon>{{ user["email"] }}<br>
                <v-icon>mdi-account-group </v-icon>{{ user["department"] }}
              </v-list-item-subtitle>
            </v-col>

          </v-row>


          </Link>
        </div>
      </v-list>



      <v-card-text>
        <v-pagination v-model="page" :length="length" @input="changePage"></v-pagination>
      </v-card-text>


      <v-divider></v-divider>
    </v-card>


    <!-- 写真表示 -->
    <v-dialog v-model="imageDialog" :max-width="$vuetify.breakpoint.smAndUp ? '600px' : 'unset'"
      @click:outside="fileDialog = false;">
      <v-card flat tile>

        <v-img v-bind:src="`/storage/user/${profile_img}`" class="grey lighten-2" height="auto" v-if="profile_img">
          <template v-slot:placeholder>
            <v-row class="ma-0" align="center" justify="center">
              <v-progress-circular indeterminate></v-progress-circular>
            </v-row>
          </template>
        </v-img>

      </v-card>
    </v-dialog>


  </Layout>
</template>


<style>
.v-carousel__controls {
  background: rgba(0, 0, 0, 0.1);
}
</style>

<script>
import Layout from "./Layout";
import { Link } from "@inertiajs/inertia-vue";

export default {
  components: { Layout, Link },

  props: ["users", "form_params", "productList", 'user'],



  data() {
    return {

      // 検索ダイアログ
      searchDialog: false,
      form: this.$inertia.form(this.form_params),

      profile_img: undefined,
      icon_img: undefined,
      imageDialog: false,

      // 設定されている検索条件の件数
      formParamsCount: Object
        .entries(this.form_params)
        .filter(function (param) {
          return param[1] && param[1].length !== 0;
        }).length,

      page: Number(this.users['current_page']),
      length: Number(this.users['last_page']),
    };
  },


  methods: {

    // 絞り込み実行
    search() {
      this.form
        .transform((data) => {
          // 値が空の要素を削除
          Object.entries(data).forEach((kv) => {
            if (kv[1] === "" || kv[1] === null) {
              delete data[kv[0]];
            }
          });

          return { ...data };
        })
        .get(this.$route('users.index'), {
          onSuccess: () => {
            this.closeSearchDialog();
          },
        });
    },


    changePage() {
      // サーバ側で生成された検索パラメータを含む最終ページURLを取得
      let url = new URL(this.users["last_page_url"]);

      // ページ数の書き換え
      if (this.page !== 1) {
        url.searchParams.set('page', String(this.page));
      } else {
        url.searchParams.delete('page');
      }

      // ページ移動
      this.$inertia.get(url.href);
    },

    // ダイアログを閉じる
    closeSearchDialog() {
      //  ダイアログを閉じる
      this.searchDialog = false;
    },
  

    clickImage(profile_img) {
      this.profile_img = profile_img;
      this.imageDialog = true;
    },
  },

}
</script>
