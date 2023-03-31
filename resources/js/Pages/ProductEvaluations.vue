<template>
  <Layout>
    <v-card
        flat tile
    >
      <v-card-title>
        <v-icon dark left>mdi-alphabetical-variant</v-icon>
        商材の評価
      </v-card-title>

      <v-card-text>
        <v-row>
          <v-col>
            <Link
                as="Button"
                :small="$vuetify.breakpoint.xs"
                @click.native="searchDialog = true"
                :color="formParamsCount ? 'warning' : undefined"
            >
              <v-icon left>
                mdi-filter-outline
              </v-icon>
              期間指定
              <template v-if="formParamsCount">
                ({{ form_params['date_start'] }}<span v-if="form_params['date_start'] && form_params['date_end']"> ... </span>{{ form_params['date_end'] }})
              </template>
            </Link>
          </v-col>
        </v-row>
      </v-card-text>

      <v-divider class="mx-4"></v-divider>

      <!-- グラフ表示 -->
      <v-card-text>
        <v-row class="justify-space-around" style="gap: 16px 16px;">
          <template v-if="product_evaluations.length">
            <v-col cols="auto" :style="{'min-height': getChartWidth + 'px'}" v-for="(product_evaluation, index) in product_evaluations" :key="index">
              <div class="text-h6" :style="{'min-width': getChartWidth + 'px'}">{{ product_evaluation['product']['name'] }}</div>

              <template v-if="product_evaluation['count']">
                <!-- 円グラフ 期間指定時にv-ifを利用してチャートコンポーネントを再描画する -->
                <ProductEvaluationChart
                    :width="getChartWidth" :height="getChartWidth"
                    :labels=evaluationLabels
                    :count="Object.values(product_evaluation['evaluation_clients_count'])"
                    v-if="!loading['search']"
                ></ProductEvaluationChart>

                <!-- チャートコンポーネント再描画中のダミー表示 -->
                <div v-else :style="{ height: getChartWidth + 'px' }"></div>

                <!-- 会社リスト -->
                <v-list class="mt-2" :style="{width: getChartWidth + 'px'}">
                  <v-list-item
                      link
                      v-for="(evaluation_client, index) in product_evaluation['evaluation_clients']" :key="index"
                      @click.native="clientsDialogOpen(
                        product_evaluation['product']['name'],
                        evaluation_client['evaluation']['grade'],
                        evaluation_client['evaluation']['label'],
                        evaluation_client['latest_evaluations'],
                        evaluation_client['sales_methods_count'],
                      )"
                  >
                    <v-row>
                      <v-col class="my-0 py-0" cols="8">
                        <EvaluationIcon :evaluation="evaluation_client['evaluation']['grade']"></EvaluationIcon>
                        {{ evaluation_client['evaluation']['label'] }}
                      </v-col>
                      <v-col class="my-0 py-0" cols="4">
                        {{ (evaluation_client['latest_evaluations_count'] / product_evaluation['count'] * 100).toFixed(2) }}% ({{ evaluation_client['latest_evaluations_count'] }})
                      </v-col>
                    </v-row>
                  </v-list-item>
                </v-list>
              </template>

              <template v-else>
                評価はありません
              </template>
            </v-col>
          </template>

          <template v-else>
            <v-col cols="auto mt-10">
              担当している商材がありません
            </v-col>
          </template>
        </v-row>

        <v-divider class="my-8"></v-divider>

        <v-list-item>
          <ul>
            <li>自分の日報から各商材の最新の評価を集計して表示しています。担当していない商材のグラフは表示されません。</li>
            <li>複数の日報で同じ会社に商材の評価を設定した場合、日付が最も新しい日報の評価が最新の評価となります。</li>
            <li>新しい日報を作成時に、ある商材Aを評価無しで設定した場合、それより古い日報に商材Aの評価があればそちらが最新の評価となります。</li>
          </ul>
        </v-list-item>

        <v-divider class="my-8"></v-divider>
      </v-card-text>

      <v-card-text class="text-right">
        <BackButton></BackButton>
      </v-card-text>
    </v-card>

    <!-- 会社リスト -->
    <v-dialog
        v-model="clientsDialog"
        :max-width="$vuetify.breakpoint.smAndUp ? '600px' : 'unset'"
    >
      <v-card flat tile>
        <v-card-title>
          {{ clientsDialogProductName }}
        </v-card-title>

        <v-card-text>
          <div class="mb-2">
            <EvaluationIcon :evaluation="clientsDialogEvaluation"></EvaluationIcon>
            {{ clientsDialogEvaluationLabel }} ({{ Object.keys(clientsDialogLatestEvaluations).length }}件)
          </div>

          <div class="mb-4 text-caption">
            <template v-for="sales_method in clientsDialogSalesMethodsCount">
              <div class="d-inline-block ml-2">{{ sales_method.name }}:{{ sales_method.count }}件</div>
            </template>
          </div>

          <v-list
              dense class="pa-0"
              v-if="Object.keys(clientsDialogLatestEvaluations).length"
          >
            <template v-for="(latestEvaluation, index) in clientsDialogLatestEvaluations">
              <v-divider :key="'divider' + index" v-if="index !== 0"></v-divider>

              <Link as="v-list-item" :key="index" :href="$route('reports.show', {report: latestEvaluation.report_content.report_id})">
                <v-list-item-content>
                  <v-list-item-subtitle>

                    <template v-if="!$vuetify.breakpoint.xs">
                      <v-row>
                        <v-col class="text-truncate" cols="8">
                          {{ latestEvaluation.report_content.client.name }}
                        </v-col>
                        <v-col class="text-truncate text-caption" cols="4">
                          {{ latestEvaluation.report_content.sales_method.name }}
                        </v-col>
                      </v-row>
                    </template>
                    <template v-else>
                      {{ latestEvaluation.report_content.client.name }}
                      <div class="text-caption">{{ latestEvaluation.report_content.sales_method.name }}</div>
                    </template>
                  </v-list-item-subtitle>
                </v-list-item-content>
              </Link>
            </template>
          </v-list>
          <div v-else>
            まだ評価はありません
          </div>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-text class="text-right">
          <Button
              class="mt-4"
              :small="$vuetify.breakpoint.xs"
              @click.native="clientsDialog = false"
          >
            <v-icon>
              mdi-close
            </v-icon>
            閉じる
          </Button>
        </v-card-text>
      </v-card>
    </v-dialog>

    <!-- 期間指定ダイアログ -->
    <v-dialog
        v-model="searchDialog"
        :max-width="$vuetify.breakpoint.smAndUp ? '600px' : 'unset'"
    >
      <v-card flat tile>
        <v-card-title>
          期間指定
        </v-card-title>

        <form @submit.prevent="search">
          <v-card-text>
            <v-list>
              <v-list-item>
                <v-text-field
                    dense filled
                    prepend-inner-icon="mdi-calendar"
                    label="開始日"
                    type="date"
                    name="date"
                    v-model="form['date_start']"
                    maxlength="200"
                    :error="Boolean(form.errors['date_start'])"
                    :error-messages="form.errors['date_start']"
                ></v-text-field>
              </v-list-item>

              <v-list-item>
                <v-text-field
                    dense filled
                    prepend-inner-icon="mdi-calendar"
                    label="終了日"
                    type="date"
                    name="date"
                    v-model="form['date_end']"
                    maxlength="200"
                    :error="Boolean(form.errors['date_end'])"
                    :error-messages="form.errors['date_end']"
                ></v-text-field>
              </v-list-item>
            </v-list>
          </v-card-text>

          <v-card-text class="text-center">
            <Button
                :small="$vuetify.breakpoint.xs"
                type="submit"
                :loading="loading['search']"
            >
              <v-icon left>
                mdi-magnify
              </v-icon>
              この条件で検索する
            </Button>
          </v-card-text>
        </form>

        <v-divider></v-divider>

        <v-card-text class="text-right">
          <Button
              class="mt-4"
              :small="$vuetify.breakpoint.xs"
              @click.native="searchDialog = false"
          >
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
import {Doughnut} from 'vue-chartjs'
import Layout from "./Layout";
import {Link} from "@inertiajs/inertia-vue";

export default {
  components: {Layout, Link},
  extends: Doughnut,

  props: ['product_evaluations', 'evaluations', 'sales_methods', 'form_params'],

  data() {
    return {
      // 会社リストダイアログ
      clientsDialog: false,
      clientsDialogProductName: null,
      clientsDialogEvaluation: null,
      clientsDialogEvaluationLabel: null,
      clientsDialogLatestEvaluations: [],
      clientsDialogSalesMethodsCount: [],

      // 検索ダイアログ
      searchDialog: false,
      form: this.$inertia.form(this.form_params),

      loading: {}
    }
  },

  computed: {
    // グラフに送信する評価のラベル
    evaluationLabels: function () {
      return this.evaluations.map((evaluation) => {
        return evaluation["grade"];
      })
    },

    // グラフ領域のサイズ 最大360px
    getChartWidth: function () {
      const WidthFromWindow = Math.trunc(window.innerWidth * 0.9);
      return (360 > WidthFromWindow) ? WidthFromWindow : 360;
    },

    // 設定されている検索条件の件数
    formParamsCount:
        function () {
          return Object
              .entries(this.form_params)
              .filter(function (param) {
                return param[1] && param[1].length !== 0;
              }).length
        }
  },

  methods: {
    // 会社リストを開く
    clientsDialogOpen: function (clientsDialogProductName, clientsDialogEvaluation, clientsDialogEvaluationLabel, clientsDialogLatestEvaluations, clientsDialogSalesMethodsCount) {
      this.clientsDialogProductName = clientsDialogProductName;
      this.clientsDialogEvaluation = clientsDialogEvaluation;
      this.clientsDialogEvaluationLabel = clientsDialogEvaluationLabel;
      this.clientsDialogLatestEvaluations = clientsDialogLatestEvaluations;
      this.clientsDialogSalesMethodsCount = clientsDialogSalesMethodsCount;
      this.clientsDialog = true;
    },

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

            return {...data};
          })
          .post(this.$route('product-evaluations'), {
            onStart: () => this.$set(this.loading, "search", true),
            onSuccess: () => this.closeSearchDialog(),
            onFinish: () => this.$set(this.loading, "search", false),
          });
    },

    // ダイアログを閉じる
    closeSearchDialog() {
      //  ダイアログを閉じる
      this.searchDialog = false;
    },
  }
}
</script>
