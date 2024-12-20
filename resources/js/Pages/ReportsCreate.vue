<template>
  <Layout>
    <v-card flat tile>
      <v-card-title>
        <v-icon dark left>mdi-notebook</v-icon>
        日報の作成
      </v-card-title>

      <!-- 日付 -->
      <v-card-text class="mt-4 pb-0">
        <div class=report-description-list>
          <v-row>
            <v-col cols="12" sm="4" class="align-self-center">
              <h4>日報の日付</h4>
            </v-col>

            <v-col>
              <v-text-field prepend-icon="mdi-calendar" type="date" name="date" v-model="form.date" required
                maxlength="200" :error="Boolean(form.errors.date)" :error-messages="form.errors.date"></v-text-field>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" sm="4" class="align-self-center">
              <h4>公開/非公開</h4>
              <span>※デフォルトは公開</span>
            </v-col>

            <v-col>
              <v-switch dense filled class="ma-0 pa-0" color="warning" :label="(form.is_private) ? '非公開' : '公開'"
                :prepend-icon="(form.is_private) ? 'mdi-eye-off' : 'mdi-eye'" name="is_private"
                v-model="form.is_private" required :error="Boolean(form.errors.is_private)"
                :error-messages="form.errors.is_private"></v-switch>
            </v-col>
          </v-row>


          <!-- <v-row>
            <v-col cols="12" sm="4" class="align-self-center">
              <h4>ファイル</h4>
            </v-col>

            <v-col>
              <v-file-input chips prepend-icon="" multiple prepend-inner-icon="mdi-paperclip" name="file_name"
                id="file_name" label="ファイルを選択する" v-model="form.file_name"
                accept="image/*, .pdf , .csv, .txt ,.xlsx , .xlsm"></v-file-input>
            </v-col>
          </v-row> -->

        </div>
      </v-card-text>

      <v-divider class="mx-4 my-4" v-if="form['report_contents'].length"></v-divider>

      <!-- 報告リスト -->
      <v-card-text v-for="(report_content, index) in form['report_contents']" :key="index">
        <div class=report-description-list>
          <v-row class="grey lighten-3 px-6">
            <v-col>
              <h3>
                <template v-if="report_content.type === 'work'">業務日報</template>
                <template v-if="report_content.type === 'sales'">営業日報</template>
              </h3>
            </v-col>
          </v-row>

          <v-row>
            <!-- 左カラム -->
            <v-col cols="12" sm="4">
              <template v-if="report_content.type === 'work'">
                <h4 class="mb-1">仕事内容</h4>
                {{ report_content.title }}
              </template>

              <template v-if="report_content.type === 'sales'">

                <template v-if="!report_content.free">

                  <h4 class="mb-1">会社</h4>
                  <div class="d-flex align-center">


                    <div>
                      <div>
                        <Link :href="$route('clients.show', { client: report_content.client.id })">
                        {{ report_content['client']['name'] }}
                        </Link>
                      </div>
                      <div class="mt-1" v-if="report_content['branch']">
                        {{ report_content["branch"]["name"] }}
                      </div>
                      <div class="mt-1">
                        {{ report_content["client"]["client_type_name"] }}
                      </div>
                    </div>
                  </div>


                  <template v-if="report_content['contact_persons'].length > 0">
                    <h4 class="mt-2 mb-1">面談者</h4>
                    <template v-for="contact_person in report_content['contact_persons']">
                      <div class="mb-2">{{ contact_person.name }} <br /><span style="font-size: small;">({{
                        contact_person.department }} / {{ contact_person.position }})<br /></span></div>
                      <!-- <v-divider></v-divider> -->
                    </template>

                    <!-- <template v-if="report_content.participants">
  {{ report_content["participants"] }}
</template> -->

                  </template>


                </template>

                <!-- フリー営業 -->
                <template v-else>

                  <h4 class="mt-1">会社</h4>
                  {{ report_content['client_name'] }}


                  <template v-if="report_content.departments || report_content.position">
                    <h4 class="mt-1">部署名 / 役職</h4>
                    {{ report_content["departments"] ?? '-' }} / {{ report_content["position"] ?? '-' }}
                  </template>


                  <template v-if="report_content.participants">
                    <h4 class="mt-2">面談者</h4>
                    {{ report_content['participants'] }}
                  </template>

                </template>


                <template v-if="report_content.sales_method">
                  <h4 class="mt-2 mb-1">営業手段</h4>
                  {{ report_content["sales_method"]["name"] }}
                </template>

                <h4 class="mt-2 mb-1">商談所要時間</h4>
                {{ report_content.required_time }}




              </template>



            </v-col>

            <!-- 右カラム -->
            <v-col>
              <template v-if="report_content.type === 'work'">
                <h4 class="mb-1">
                  本文
                  <v-chip x-small color="error" class="ml-2" v-if="Number(report_content.is_complaint)">
                    クレーム・トラブル
                  </v-chip>

                  <v-chip x-small color="#a9d6fc" class="ml-2" v-if="Number(report_content.is_zaitaku)">
                    在宅
                  </v-chip>

                </h4>
              </template>

              <template v-if="report_content.type === 'sales'">
                <h4 class="mb-1">
                  面談内容
                  <v-chip x-small color="error" class="ml-2" v-if="Number(report_content.is_complaint)">
                    クレーム・トラブル
                  </v-chip>
                </h4>
              </template>

              <div>
                <span style="white-space: pre-line;">{{ report_content.description }}</span>
              </div>

              <!-- <div v-if="report_content.file_name">
                <h4 class="mb-1">
                ファイル
              </h4>
              <span>{{ report_content.file_name.name }}</span>
              </div> -->

              <template v-if="Object.values(report_content['product_evaluation']).find(v => Boolean(v))">
                <h4 class="mt-2 mb-1">商材の評価</h4>
                <div v-for="(evaluation_id, product_id) in report_content['product_evaluation']" :key="product_id">
                  <template v-if="evaluation_id">
                    <EvaluationIcon
                      :evaluation="evaluations.find(evaluation => evaluation.id === Number(evaluation_id))['grade']">
                    </EvaluationIcon>
                    {{ products.find(product => product.id === Number(product_id))["name"] }}
                  </template>
                </div>
              </template>

              <h4 class="mt-2 mb-1" v-if='report_content["product_description"]'>商材評価の備考欄</h4>
              {{ report_content["product_description"] }}

            </v-col>
          </v-row>
        </div>

        <v-row>
          <v-col cols="12" class="text-right">
            <Button :small="$vuetify.breakpoint.xs" @click.native="editReportContent(index)"
              :dusk="report_content.type === 'sales' ? 'reportContentSalesDialog' : 'reportContentWorkDialog'">
              <v-icon left>
                mdi-pencil
              </v-icon>
              この報告を編集
            </Button>

            <Button class="ml-2" color="error" :disabled="form['report_contents'].length <= 1"
              :small="$vuetify.breakpoint.xs" @click.native.prevent="reportContentDelete(index)">
              <v-icon left>
                mdi-delete-outline
              </v-icon>
              削除
            </Button>
          </v-col>
        </v-row>
      </v-card-text>

      <v-divider class="mx-4 my-4"></v-divider>


      <!-- ファイル表示 -->
      <v-container fluid v-if="this.reportAttachFileContentDisplay">
        <v-row>
          <template v-for="(file, c_index) in this.reportAttachFileContentDisplay">
            <v-col cols="3" class="text-al" v-if="file.type == 'image' && !$vuetify.breakpoint.xs" :key="file.name">
              <template>
                <v-card class="mx-auto" max-width="400">
                  <v-img color="surface-variant" max-height="250" min-height="150" cover
                    :src="`/storage/report/${file.name}`">

                    <div class="text-right">
                      <v-icon color="error" class="text-right" @click.native="clickDeleteReportAttachFile(c_index)">
                        mdi-close-circle-outline</v-icon>
                    </div>
                  </v-img>

                </v-card>
              </template>
            </v-col>

            <v-col cols="12" class="text-al" v-if="file.type == 'image' && $vuetify.breakpoint.xs" :key="file.name">
              <template>
                <v-card class="mx-auto" max-width="400">
                  <v-img color="surface-variant" max-height="250" min-height="150" cover
                    :src="`/storage/report/${file.name}`">

                    <div class="text-right">
                      <v-icon color="error" class="text-right" @click.native="clickDeleteReportAttachFile(c_index)">
                        mdi-close-circle-outline</v-icon>
                    </div>
                  </v-img>

                </v-card>
              </template>
            </v-col>

          </template>

        </v-row>
      </v-container>

      <template v-if="this.reportAttachFileContentDisplay">
        <div v-for="(file, c_index) in this.reportAttachFileContentDisplay" :key="file.name">
          <v-row class="my-4" v-if="file.type == 'file'">
            <a :href="`/storage/report/${file.name}`" target="_blank" class="ml-8">
              <div class="text-left">
                {{ file.original_name }}
              </div>
            </a>
            <v-icon color="error" class="ml-3" @click.native="clickDeleteReportAttachFile(c_index)">
              mdi-close-circle-outline</v-icon>
          </v-row>

        </div>
      </template>


      <v-card-text class="text-right" style="margin-top:-30px;">
        <div>
          <Button center :small="$vuetify.breakpoint.xs" @click.native="openReportFileModal">
            <v-icon left>
              mdi-plus
            </v-icon>
            添付ファイル
          </Button>
        </div>
      </v-card-text>


      <v-row>
        <!-- 追加ボタン -->
        <v-card-text class="text-center">
          <div>
            <Button center :small="$vuetify.breakpoint.xs" @click.native="openReportContentForm('sales')">
              <v-icon left>
                mdi-plus
              </v-icon>
              営業日報を追加する
            </Button>
          </div>

          <div class="mt-2">
            <Button center :small="$vuetify.breakpoint.xs" @click.native="openReportContentForm('work')">
              <v-icon left>
                mdi-plus
              </v-icon>
              業務日報を追加する
            </Button>
          </div>
        </v-card-text>

      </v-row>

      <!-- 日報作成ボタン -->
      <v-card-text class="text-center" v-if="form.report_contents.length">
        <Button center color="primary" :small="$vuetify.breakpoint.xs" @click.native="create(0)"
          :loading="loading['create']">
          <v-icon left>
            mdi-content-save-outline
          </v-icon>
          この内容で日報を作成する
        </Button>
      </v-card-text>

      <v-card-text class="text-right" v-if="form.report_contents.length">
        <Button center :small="$vuetify.breakpoint.xs" @click.native="create2(1)" :loading="loading['create2']">
          <v-icon left>
            mdi-content-save-outline
          </v-icon>
          下書きとして保存する
        </Button>
      </v-card-text>

      <v-divider></v-divider>

      <v-card-text class="text-right">
        <BackButton></BackButton>
      </v-card-text>
    </v-card>

    <!-- 報告追加・編集ダイアログ -->
    <v-dialog :max-width="$vuetify.breakpoint.smAndUp ? '600px' : 'unset'" v-model="dialog" persistent>
      <v-card flat tile>
        <form @submit.prevent="reportContentMode === 'create' ? addReportContent() : updateReportContent()">

          <v-card-title>
            <span v-html="reportContentForm.type === 'work' ? '業務' : '営業'"></span>日報の<span
              v-html="reportContentMode === 'create' ? '追加' : '編集'"></span>

            <span class="ml-auto" v-if="reportContentForm.type === 'sales'">
              <a href="/client-types/clients/create" target="_blank">
                <v-btn tile depressed color="#969797" dark :small="$vuetify.breakpoint.xs">
                  会社登録
                </v-btn>
              </a>
            </span>

          </v-card-title>



          <v-card-text>
            <v-list>
              <!-- 会社リストの絞り込み -->
              <template v-if="reportContentForm.type === 'sales'">

                <div>
                  <v-checkbox dense label="フリー営業" v-model="reportContentForm.free"></v-checkbox>
                </div>


                <v-divider v-if="reportContentForm.type === 'sales'" class="mb-4"></v-divider>

                <template v-if="!reportContentForm.free">

                  <div class="mx-8">
                    <v-list-item>
                      <v-text-field dense outlined clearable prepend-inner-icon="mdi-pencil" label="会社名(ふりがな)から検索"
                        hint="文字を含む会社だけをリストに表示します" persistent-hint name="clientsFilterForm.name"
                        v-model="clientsFilterForm.name" maxlength="200" @input="getClients()"></v-text-field>
                    </v-list-item>


                    <!-- <v-list-item>
                    <v-text-field dense outlined clearable prepend-inner-icon="mdi-pencil" label="フリーワードから検索"
                      hint="複数のワードで検索する場合は、スペースを開けてください" persistent-hint name="clientsFilterForm.word"
                      v-model="clientsFilterForm.word" maxlength="200" @input="getClients()"></v-text-field>
                  </v-list-item> -->


                    <v-list-item>
                      <v-autocomplete dense outlined clearable label="都道府県" hint="指定の都道府県に該当する会社だけをリストに表示します"
                        persistent-hint :items="prefecture" name="clientsFilterForm.prefecture"
                        v-model="clientsFilterForm.prefecture" @change="getClients()">
                      </v-autocomplete>
                    </v-list-item>


                    <v-list-item>
                      <v-checkbox dense outlined label="過去自身で日報記載あり" @change="getClients();"
                        v-model="clientsFilterForm.my_charge"
                        :disabled="!clientsFilterForm.prefecture && !clientsFilterForm.name">
                      </v-checkbox>
                    </v-list-item>



                    <!-- <v-list-item>
                    <v-select dense outlined clearable label="会社種別" hint="該当する会社だけをリストに表示します" persistent-hint
                      :items="client_types" item-value="id" item-text="name" name="clientsFilterForm.client_type_id"
                      v-model="clientsFilterForm.client_type_id"
                      @change="clientsFilterForm.client_type_taxibus_category = ''; clientsFilterForm.genre_id = ''; getClients()">
                    </v-select>
                  </v-list-item> -->

                    <!-- <v-list-item>
                    <v-select dense outlined clearable label="カテゴリー(タクシー・バス会社のみ)" hint="該当する会社だけをリストに表示します"
                      persistent-hint :items="client_type_taxibus_categories" item-value="id" item-text="name"
                      name="clientsFilterForm.client_type_taxibus_category"
                      v-model="clientsFilterForm.client_type_taxibus_category"
                      :disabled="clientsFilterForm.client_type_id !== 'taxibus'" @change="getClients()">
                    </v-select>
                  </v-list-item>

                  <v-list-item>
                    <v-select dense outlined clearable label="ジャンル" hint="該当する会社だけをリストに表示します" persistent-hint
                      :items="genresFiltered" item-value="id" item-text="name" name="clientsFilterForm.genre_id"
                      v-model="clientsFilterForm.genre_id" :disabled="!Boolean(clientsFilterForm.client_type_id)"
                      @change="getClients()">
                    </v-select>
                  </v-list-item> -->
                  </div>

                  <v-divider class="mb-3"></v-divider>

                  <v-list-item class="font-weight-bold">
                    <div>
                      <template v-if="clients_count">
                        {{ clients_total_count }}件中<span class="red--text">{{ clients_count }}</span>件中該当
                        <template v-if="this.$page.props.errors.event">
                          <br>{{ this.$page.props.errors.event }}
                        </template>
                      </template>
                      <template v-else>
                        会社を検索する条件を指定してください
                      </template>
                    </div>
                  </v-list-item>

                  <!-- 会社リスト -->
                  <v-list-item>
                    <v-select v-if="!clientsListEnable" dense filled clearable label="会社" disabled>
                    </v-select>
                    <v-select v-if="clientsListEnable" dense filled clearable label="会社" :items="clients"
                      item-value="id" name="client_id" v-model="reportContentForm.client_id"
                      :error="Boolean(reportContentFormError.client_id)"
                      :error-messages="reportContentFormError.client_id"
                      @change="reportContentFormError.client_id = undefined">
                      <!-- カスタム選択済み表示 -->
                      <template v-slot:selection="{ item }">

                        <template v-if="item.name_position == '前' || item.name_position == '後ろ'">
                          <template v-if="item.name_position == '前'">
                            {{ item.type_name }} {{ item.name }}
                          </template>

                          <template v-if="item.name_position == '後ろ'">
                            {{ item.name }} {{ item.type_name }}
                          </template>

                        </template>

                        <template v-else>
                          {{ item.name }}
                        </template>

                        <span v-if="item.prefecture" class="font-weight-thin text-body-2 ml-2">({{
                          item.prefecture
                          }})</span>


                        <!-- <span v-if="item.prefecture" class="font-weight-thin text-body-2">{{ item.prefecture }}</span> <span v-if="item.address" class="font-weight-thin">{{ item.address }}</span> -->
                      </template>
                      <!-- カスタム選択肢表示 -->
                      <template v-slot:item="{ item }">

                        <template v-if="item.name_position == '前' || item.name_position == '後ろ'">
                          <template v-if="item.name_position == '前'">
                            {{ item.type_name }} {{ item.name }}
                          </template>

                          <template v-if="item.name_position == '後ろ'">
                            {{ item.name }} {{ item.type_name }}
                          </template>

                        </template>

                        <template v-else>
                          {{ item.name }}
                        </template>

                        <span v-if="item.prefecture" class="font-weight-thin text-body-2 ml-2">({{
                          item.prefecture }})</span>
                      </template>
                    </v-select>
                  </v-list-item>


                  <template v-if="reportContentForm.client_id">
                    <v-list-item style="margin-top: -20px;margin-bottom: 10px;">

                      <span class="ml-auto">

                        <a :href="'/clients/' + reportContentForm.client_id" target="_blank">
                          <v-btn tile depressed color="#969797" dark small>
                            会社情報
                          </v-btn>
                        </a>

                        <a :href="'/clients/' + reportContentForm.client_id + '/edit'" target="_blank">
                          <v-btn tile depressed color="#969797" dark small>
                            会社編集
                          </v-btn>
                        </a>

                      </span>
                    </v-list-item>


                    <!-- 営業所リスト -->
                    <v-list-item>

                      <v-select dense filled clearable label="営業所" :items="branches" item-value="id" name="branch_id"
                        v-model="reportContentForm.branch_id" :error="Boolean(reportContentFormError.branch_id)"
                        :error-messages="reportContentFormError.branch_id"
                        @change="reportContentFormError.branch_id = undefined">
                        <!-- カスタム選択済み表示 -->
                        <template v-slot:selection="{ item }">
                          {{ item.name }}
                        </template>
                        <!-- カスタム選択肢表示 -->
                        <template v-slot:item="{ item }">
                          {{ item.name }}
                        </template>
                      </v-select>

                    </v-list-item>

                    <v-list-item style="margin-top: -20px;margin-bottom: 10px;">

                      <span class="ml-auto">

                        <a :href="'/clients/' + reportContentForm.client_id + '/branches/create'" target="_blank">
                          <v-btn tile depressed color="#969797" dark small>
                            営業所を新規追加
                          </v-btn>
                        </a>

                        <v-btn tile depressed color="#969797" dark small
                          @click="getClients(reportContentForm.client_id)">
                          <v-icon>
                            mdi-reload
                          </v-icon></v-btn>

                      </span>
                    </v-list-item>


                    <v-list-item>

                      <v-select dense multiple chips filled clearable label="担当者" :items="contact_persons"
                        item-value="id" name="contact_person_id" v-model="reportContentForm.contact_person_id"
                        :error="Boolean(reportContentFormError.contact_person_id)"
                        :error-messages="reportContentFormError.contact_person_id"
                        @change="reportContentFormError.contact_person_id = undefined">
                        <!-- カスタム選択済み表示 -->
                        <template v-slot:selection="{ item }">
                          <!-- {{ item.name }} -->
                          {{ item.name }} ,
                        </template>
                        <!-- カスタム選択肢表示 -->
                        <template v-slot:item="{ item }">
                          <!-- {{ item.name }} -->
                          {{ item.name }} / ({{ item.department }} | {{ item.position }})
                        </template>
                      </v-select>

                    </v-list-item>

                    <v-list-item style="margin-top: -20px;margin-bottom: 10px;">

                      <span class="ml-auto">

                        <a :href="'/clients/' + reportContentForm.client_id + '?contact_person=create'" target="_blank">
                          <v-btn tile depressed color="#969797" dark small>
                            担当者を新規追加
                          </v-btn>
                        </a>

                        <v-btn tile depressed color="#969797" dark small
                          @click="getClients(reportContentForm.client_id)">
                          <v-icon>
                            mdi-reload
                          </v-icon></v-btn>

                      </span>
                    </v-list-item>
                  </template>

                </template>

              </template>



              <!-- フリー入力 -->
              <template v-if="reportContentForm.free">
                <v-list-item>
                  <v-text-field dense filled prepend-inner-icon="mdi-pencil" label="会社名" maxlength="300"
                    name="client_name" v-model="reportContentForm.client_name"
                    :error="Boolean(reportContentFormError.client_name)"
                    :error-messages="reportContentFormError.client_name"
                    @change="reportContentFormError.client_name = undefined"></v-text-field>
                </v-list-item>

                <v-list-item>
                  <v-text-field dense filled prepend-inner-icon="mdi-pencil" label="部署名" maxlength="300"
                    name="department" v-model="reportContentForm.department"></v-text-field>
                </v-list-item>

                <v-list-item>
                  <v-text-field dense filled prepend-inner-icon="mdi-pencil" label="役職" maxlength="300" name="position"
                    v-model="reportContentForm.position"></v-text-field>
                </v-list-item>


                <!-- 面談者 -->
                <v-list-item>
                  <v-text-field dense filled prepend-inner-icon="mdi-pencil" label="面談者" maxlength="200"
                    name="participants" v-model="reportContentForm.participants"
                    :error="Boolean(reportContentFormError.participants)"
                    :error-messages="reportContentFormError.participants"
                    @change="reportContentFormError.participants = undefined"></v-text-field>
                </v-list-item>


              </template>


              <v-divider class="my-4" v-if="reportContentForm.type === 'sales'"></v-divider>




              <!-- 仕事内容 -->
              <v-list-item v-if="reportContentForm.type === 'work'">
                <v-text-field dense filled prepend-inner-icon="mdi-pencil" label="仕事内容" maxlength="300" name="title"
                  v-model="reportContentForm.title" :error="Boolean(reportContentFormError.title)"
                  :error-messages="reportContentFormError.title"
                  @change="reportContentFormError.title = undefined"></v-text-field>
              </v-list-item>






              <!-- 部署名 -->
              <!-- <v-list-item v-if="reportContentForm.type === 'sales'">
                <v-text-field dense filled prepend-inner-icon="mdi-pencil" label="部署名" maxlength="200"
                  name="departments" v-model="reportContentForm.departments"></v-text-field>
              </v-list-item> -->

              <!-- 役職名 -->
              <!-- <v-list-item v-if="reportContentForm.type === 'sales'">
                <v-text-field dense filled prepend-inner-icon="mdi-pencil" label="役職名" maxlength="200" name="position"
                  v-model="reportContentForm.position"></v-text-field>
              </v-list-item> -->






              <!-- 仕事本文内容/面談内容 -->
              <v-list-item>
                <v-textarea dense filled counter="300" maxlength="300" prepend-inner-icon="mdi-pencil"
                  :label="(reportContentForm.type === 'work') ? '本文' : '面談内容'" name="description"
                  v-model="reportContentForm.description" :error="Boolean(reportContentFormError.description)"
                  :error-messages="reportContentFormError.description"
                  @change="reportContentFormError.description = undefined"></v-textarea>
              </v-list-item>

              <!-- 営業手段 -->
              <v-list-item v-if="reportContentForm.type === 'sales'">
                <v-select dense filled clearable label="営業手段" :items="sales_methods" item-value="id"
                  v-model="reportContentForm.sales_method_id" :error="Boolean(reportContentFormError.sales_method_id)"
                  :error-messages="reportContentFormError.sales_method_id"
                  @change="reportContentFormError.sales_method_id = undefined">
                  <!-- カスタム選択済み表示 -->
                  <template v-slot:selection="{ item }">
                    {{ item.name }}
                  </template>
                  <!-- カスタム選択肢表示 -->
                  <template v-slot:item="{ item }">
                    {{ item.name }}
                  </template>
                </v-select>
              </v-list-item>


              <!-- 商談所要時間 -->
              <v-list-item v-if="reportContentForm.type === 'sales'">

                <v-select dense filled label="商談所要時間" :items="required_time" name="required_time" clearable
                  v-model="reportContentForm.required_time" :error="Boolean(reportContentFormError.required_time)"
                  :error-messages="reportContentFormError.required_time"
                  @change="reportContentFormError.required_time = undefined"></v-select>
              </v-list-item>

              <!-- クレーム・トラブル -->
              <v-list-item>
                <v-switch dense filled class="ma-0 pa-0" color="error" label="クレーム・トラブル" name="is_complaint"
                  v-model="reportContentForm.is_complaint"></v-switch>
              </v-list-item>


              <!-- 在宅 -->
              <v-list-item v-if="reportContentForm.type === 'work'">
                <v-switch dense filled class="ma-0 pa-0" color="blue" label="在宅" name="is_zaitaku"
                  v-model="reportContentForm.is_zaitaku"></v-switch>

              </v-list-item>



              <v-divider class="my-4"></v-divider>

              <!--商材の評価-->
              <template v-if="reportContentForm.type === 'sales'">
                <v-list-item>
                  <v-list-item-subtitle>
                    商材の評価
                  </v-list-item-subtitle>
                </v-list-item>

                <v-list-item v-for="(product, index) in products" :key="index">
                  <v-select dense filled clearable :label="product.name" :items="evaluations" item-value="id"
                    v-model="reportContentForm.product_evaluation[product.id]">
                    <!-- カスタム選択済み表示 -->
                    <template v-slot:selection="{ item }">
                      <EvaluationIcon :evaluation="item.grade"></EvaluationIcon>
                      {{ item.label }}
                    </template>
                    <!-- カスタム選択肢表示 -->
                    <template v-slot:item="{ item }">
                      <EvaluationIcon :evaluation="item.grade"></EvaluationIcon>
                      {{ item.label }}
                    </template>
                  </v-select>
                </v-list-item>


                <!-- 仕事本文内容/面談内容 -->
                <v-list-item>
                  <v-textarea dense filled counter="200" maxlength="200" prepend-inner-icon="mdi-pencil" label="商材評価備考欄"
                    name="product_description" v-model="reportContentForm.product_description"></v-textarea>
                </v-list-item>


              </template>

              <!-- <v-list-item>
                <v-file-input dense filled prepend-icon="" prepend-inner-icon="mdi-paperclip" name="file_name" id="file_name" label="ファイルを選択する" v-model="reportContentForm.file_name" accept="image/*, .pdf , .csv, .txt ,.xlsx , .xlsm"></v-file-input>
              </v-list-item> -->

              <v-divider class="my-4" v-if="reportContentForm.type === 'sales'"></v-divider>

              <!-- 注釈 -->
              <v-list-item v-if="reportContentForm.type === 'sales'">
                <ul>
                  <li>営業日報は登録された会社の詳細ページにも表示されます。</li>
                </ul>
              </v-list-item>
            </v-list>
          </v-card-text>

          <v-card-text class="pt-0 text-center">
            <Button :small="$vuetify.breakpoint.xs" type="submit">
              <v-icon left v-if="reportContentMode === 'create'">
                mdi-plus
              </v-icon>
              <v-icon left v-else>
                mdi-pencil
              </v-icon>
              <span v-html="reportContentMode === 'create' ? 'この内容を追加する' : 'この内容を反映する'"></span>
            </Button>
          </v-card-text>

          <v-divider></v-divider>

          <v-card-text class="text-right">
            <Button class="mt-4" :small="$vuetify.breakpoint.xs" @click.native="dialog = false">
              <v-icon>
                mdi-close
              </v-icon>
              閉じる
            </Button>
          </v-card-text>
        </form>
      </v-card>
    </v-dialog>


    <!-- 報告追加・編集ダイアログ -->
    <v-dialog :max-width="$vuetify.breakpoint.smAndUp ? '500px' : 'unset'" v-model="reportFileDialog">
      <v-card flat tile>
        <form @submit.prevent="addReportAttachFile()">

          <v-card-title>
            <span>添付ファイル
              <!-- <li v-for="i in 10" :key="i">リスト{{i}}</li> -->
            </span>
          </v-card-title>

          <v-card-text class="pt-0 text-center mt-2" style="margin-bottom: -40px;;">
            <v-list>
              <!-- 写真 -->
              <v-list-item v-for="(file, c_index) in reportAttachFileCount" :key="c_index">
                <v-file-input dense filled chips label="ファイルを選択してください" name="file" class="report_attach_input"
                  @change.native="uploadReportAttachFile" accept="image/*, .pdf , .csv, .txt ,.xlsx , .xlsm"
                  :data-id=c_index></v-file-input>
              </v-list-item>
            </v-list>
          </v-card-text>

          <v-card-text class="mt-0 pt-0 text-right" style="margin-top: -30px;margin-right:20px;">
            <Button :small="$vuetify.breakpoint.xs" type="button" @click.native="upcountReportFile">
              <v-icon>
                mdi-plus
              </v-icon>
            </Button>
          </v-card-text>


          <v-card-text class="pt-0 text-center">
            <Button :small="$vuetify.breakpoint.xs" type="submit" v-if="uploadFlg">
              <span>この内容を反映する</span>
            </Button>

            <template v-if="!uploadFlg">
              <Button :small="$vuetify.breakpoint.xs" type="button" disabled>
                <v-progress-circular indeterminate size="20"></v-progress-circular>
                <span>画像アップ中</span>
              </Button>
            </template>

          </v-card-text>



          <v-divider></v-divider>
          <v-card-text class="text-right">
            <Button class="mt-4" :small="$vuetify.breakpoint.xs" @click.native="closeReportDialog">
              <v-icon>
                mdi-close
              </v-icon>
              閉じる
            </Button>
          </v-card-text>
        </form>
      </v-card>
    </v-dialog>

  </Layout>
</template>

<script>
import Layout from "./Layout";
import { Link } from "@inertiajs/inertia-vue";
import _ from "lodash";

export default {
  components: { Layout, Link },

  props: ['client_types', 'client_type_taxibus_categories', 'genres', 'clients_total_count', 'clients_count', 'clients', 'products', 'evaluations', 'sales_methods', 'prefecture'],

  data() {

    return {
      // 会社ページ「最近の営業日報」から遷移した場合に設定される会社ID
      // 作成ダイアログを初期状態で開く
      client_id: Number(this.$route().params["client_id"]) ?? null,

      // Inertia Formヘルパ
      form: this.$inertia.form('ReportsCreate', {
        date: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
        is_private: false,
        file_name: undefined,
        report_contents: [],
      }),

      // 報告作成・編集ダイアログ
      dialog: false,
      reportContentMode: null, // create|edit
      reportContentEditingIndex: null,

      // 追加ダイアログフォーム初期データ
      reportContentFormInit: {
        type: undefined,
        title: "",
        client_id: undefined,
        client: undefined,
        branch_id: undefined,
        branch: undefined,
        participants: undefined,
        sales_method_id: undefined,
        sales_methods: undefined,
        description: "",
        product_description: "",
        is_complaint: false,
        is_zaitaku: false,
        // file_name: {},
        product_evaluation: {},
        required_time: undefined,
        departments: undefined,
        position: undefined,

        contact_persons: undefined,
        contact_person_id: undefined,
        free: false,

        client_name: undefined,
      },

      // 追加ダイアログフォームデータ
      reportContentForm: {},

      // 追加ダイアログフォームエラー
      reportContentFormError: {
        client_id: undefined,
        sales_method_id: undefined,

        description: undefined,
        title: undefined,
        required_time: undefined,

        client_name: undefined,
        participants: undefined,
      },

      // 会社絞り込みフォーム初期データ
      clientsFilterFormInit: {
        name: "",
        client_type_id: "",
        client_type_taxibus_category: "",
        genre_id: "",

        prefecture: "",
        my_charge: false,
      },

      // 会社絞り込みフォーム
      clientsFilterForm: {},

      // 会社絞り込みフォーム 会社リスト表示
      clientsListEnable: false,

      required_time: ['15分', '30分', '45分', '60分', '75分', '90分', '120分', '150分', '180分', '210分', '240分'],

      loading: {},


      uploadFlg: true,

      //日報添付ファイル表示用
      reportAttachFileContentDisplay: [],

      reportFileDialog: false,
      reportAttachFileContent: [],
      reportAttachFileCount: 3,

      fileInit: {
        id: undefined,
        name: undefined,
        type: undefined,
        original_name: undefined,
      },

    };
  },

  computed: {
    // ジャンルリストの絞り込み
    genresFiltered: function () {
      return this.genres.filter((genre) => {
        return genre.client_type_id === this.clientsFilterForm.client_type_id;
      })
    },

    // 選択中の会社の営業所リスト
    branches: function () {
      let client = this.clients?.find(client => client.id === this.reportContentForm.client_id);

      // 新しいリストに選択された営業所が含まれなければリセット
      if (client && !client.branches.find(branch => branch.id === this.reportContentForm.branch_id)) {
        this.reportContentForm.branch = undefined;
      }

      console.log('営業所リストを返す');
      console.log(client.branches);

      // 営業所リストを返す
      return client.branches;
    },


    // // 選択中の会社の担当者リスト
    contact_persons: function () {

      let client = this.clients?.find(client => client.id === this.reportContentForm.client_id);


      // // 新しいリストに選択された営業所が含まれなければリセット
      // if (client && !client.contact_persons.find(contact_person => contact_person.id === this.reportContentForm.contact_person_id)) {
      //   this.reportContentForm.contact_persons = undefined;
      // }

      console.log('会社の担当者');
      console.log(client.contact_persons);

      return client.contact_persons;
    },
  },

  mounted() {
    // 会社ページ「この会社の営業日報を作成する」からの遷移であれば新規作成ダイアログを初期状態で開く
    if (this.client_id) {
      this.openReportContentForm("sales", this.client_id);
    }
  },

  methods: {
    // 会社リストの更新
    getClients: function (client_id = null) {
      let param;
      if (client_id) {
        // 会社IDが指定された場合は該当の会社のみがリストを取得
        param = { client_id: client_id };
      } else {
        // フォーム内容に設定された条件の会社リストを取得
        param = this.clientsFilterForm;
      }

      console.log('getClients');

      this.$inertia.post(this.$route('reports.create'), param, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['clients_count', 'clients', 'errors'],
        onSuccess: () => {
          this.clientsListEnable = true;
        },
        onError: errors => {
          this.clientsListEnable = false;

          // バリデーションエラーをトースト表示する
          // フロント側チェックを行っているため発生しない前提
          //this.$toasted.error(Object.values(errors).join("\n"), {type: 'error'});
        },
      })
    },

    // 日報コンテンツ追加ダイアログを開く
    openReportContentForm: async function (type, client_id = null) {
      // 初期値をディープコピー
      this.reportContentForm = _.cloneDeep(this.reportContentFormInit);
      this.clientsFilterForm = _.cloneDeep(this.clientsFilterFormInit);

      // 会社ページ「最近の営業日報」からの遷移であれば指定されたclient_idのみが含まれる会社リストに更新
      if (client_id) {
        await this.getClients(client_id);

        // 会社選択セレクタにclient_idを指定
        this.reportContentForm.client_id = client_id;
      } else {
        // 指定がなければ会社選択セレクタを非表示
        this.clientsListEnable = false;
      }

      // タイプを設定
      this.reportContentForm["type"] = type;

      // ダイアログを開く
      this.reportContentMode = 'create';
      this.dialog = true;
    },

    // 日報コンテンツを追加
    addReportContent: function () {
      // コンテンツ追加ダイアログはサーバ側バリデーションが行われないためrequired属性を設定してsubmit時のバリデーションを行う
      // ただしv-selectではrequired属性が動作しないため個別に入力チェックを行う



      if (this.reportContentForm.free) {

        //フリー営業の場合
        if (this.reportContentForm.type === "sales" && !this.reportContentForm.client_name) {
          this.reportContentFormError.client_name = "会社名を入力してください";
          this.$toasted.error('会社を入力してください');

          return;
        }

        if (this.reportContentForm.type === "sales" && !this.reportContentForm.participants) {
          this.reportContentFormError.participants = "面談者を入力してください";
          this.$toasted.error('面談者を入力してください');

          return;
        }

      } else {

        if (this.reportContentForm.type === "sales" && !this.reportContentForm.client_id) {
          this.reportContentFormError.client_id = "会社を選択してください";
          this.$toasted.error('会社を選択してください');

          return;
        }

      }



      // if (this.reportContentForm.type === "sales" && !this.reportContentForm.sales_method_id) {
      //   this.reportContentFormError.sales_method_id = "営業手段を選択してください";
      //   this.$toasted.error('営業手段を選択してください');

      //   return;
      // }

      if (this.reportContentForm.type === "sales" && !this.reportContentForm.description) {
        this.reportContentFormError.description = "面談内容を選択してください";
        this.$toasted.error('面談内容を選択してください');

        return;
      }



      if (this.reportContentForm.type === "work" && !this.reportContentForm.title) {
        this.reportContentFormError.title = "仕事内容を選択してください";
        this.$toasted.error('仕事内容を選択してください');

        return;
      }

      if (this.reportContentForm.type === "work" && !this.reportContentForm.description) {
        this.reportContentFormError.description = "本文を選択してください";
        this.$toasted.error('本文を選択してください');

        return;
      }



      if (this.reportContentForm.type === "sales" && !this.reportContentForm.required_time) {
        this.reportContentFormError.required_time = "商談所要時間を選択してください";
        this.$toasted.error('商談所要時間を選択してください');

        return;
      }

      if (!this.reportContentForm.free) {
        // 会社が選択されていれば会社情報を追加
        if (this.reportContentForm.client_id) {
          this.reportContentForm["client"] = this.clients.find(client => client.id === this.reportContentForm.client_id);
        }

        // 営業所が選択されていれば営業所情報を追加
        if (this.reportContentForm.branch_id) {
          this.reportContentForm["branch"] = this.branches.find(branch => branch.id === this.reportContentForm.branch_id);
        }


        // 担当者が選択されていれば担当者情報を追加
        if (this.reportContentForm.contact_person_id) {
          this.reportContentForm["contact_persons"] = this.contact_persons.filter(contact_person => this.reportContentForm.contact_person_id.includes(contact_person.id));
        }
      }


      // 営業手段が選択されていれば営業手段情報を追加
      if (this.reportContentForm.sales_method_id) {
        this.reportContentForm["sales_method"] = this.sales_methods.find(sales_method => sales_method.id === this.reportContentForm.sales_method_id);
      }

      console.log('reportContentForm');
      console.log(this.reportContentForm);



      // フォームに追加
      this.form.report_contents.push(this.reportContentForm);

      // 追加ダイアログをリセットして閉じる
      this.reportContentForm = {};
      this.reportContentMode = null;
      this.dialog = false;
    },

    // 日報コンテンツ編集
    editReportContent: function (index) {
      // 編集している対象のインデックス値を保存
      this.reportContentEditingIndex = index;

      // 設定値をディープコピー
      this.reportContentForm = _.cloneDeep(this.form.report_contents[index]);
      this.clientsFilterForm = _.cloneDeep(this.clientsFilterFormInit);

      // 指定されたclient_idのみが含まれる会社リストに更新
      if (this.reportContentForm.client_id) {
        this.getClients(this.reportContentForm.client_id);
      }

      console.log('editReportContent');
      console.log(this.reportContentForm);

      // ダイアログを開く
      this.reportContentMode = "edit";
      this.dialog = true;
    },

    // 日報コンテンツ編集の完了
    updateReportContent: function () {


      if (this.reportContentForm.free) {

        //フリー営業の場合
        if (this.reportContentForm.type === "sales" && !this.reportContentForm.client_name) {
          this.reportContentFormError.client_name = "会社名を入力してください";
          this.$toasted.error('会社を入力してください');

          return;
        }

        if (this.reportContentForm.type === "sales" && !this.reportContentForm.participants) {
          this.reportContentFormError.participants = "面談者を入力してください";
          this.$toasted.error('面談者を入力してください');

          return;
        }

      } else {

        if (this.reportContentForm.type === "sales" && !this.reportContentForm.client_id) {
          this.reportContentFormError.client_id = "会社を選択してください";
          this.$toasted.error('会社を選択してください');

          return;
        }

      }

      // コンテンツ追加ダイアログはサーバ側バリデーションが行われないためrequired属性を設定してsubmit時のバリデーションを行う
      // ただしv-selectではrequired属性が動作しないため個別に入力チェックを行う
      // if (this.reportContentForm.type === "sales" && !this.reportContentForm.sales_method_id) {
      //   this.reportContentFormError.sales_method_id = "営業手段を選択してください";
      //   this.$toasted.error('営業手段を選択してください');

      //   return;
      // }



      if (this.reportContentForm.type === "sales" && !this.reportContentForm.description) {
        this.reportContentFormError.description = "面談内容を選択してください";
        this.$toasted.error('面談内容を選択してください');

        return;
      }


      if (this.reportContentForm.type === "work" && !this.reportContentForm.description) {
        this.reportContentFormError.description = "本文を選択してください";
        this.$toasted.error('本文を選択してください');

        return;
      }


      if (this.reportContentForm.type === "work" && !this.reportContentForm.title) {
        this.reportContentFormError.title = "仕事内容を選択してください";
        this.$toasted.error('仕事内容を選択してください');

        return;
      }


      if (this.reportContentForm.type === "work" && !this.reportContentForm.description) {
        this.reportContentFormError.description = "本文を選択してください";
        this.$toasted.error('本文を選択してください');

        return;
      }




      if (this.reportContentForm.type === "sales" && !this.reportContentForm.required_time) {
        this.reportContentFormError.required_time = "商談所要時間を選択してください";
        this.$toasted.error('商談所要時間を選択してください');

        return;
      }

      // フォームに送る値を書き換える
      const report_contents_update = this.form.report_contents[this.reportContentEditingIndex];
      ["product_description", "description", "is_complaint", "is_zaitaku", "title", "client_id", "branch_id", "participants", "sales_method_id", "product_evaluation", "required_time", "departments", "position", "contact_person_id"].forEach((key) => {
        report_contents_update[key] = _.cloneDeep(this.reportContentForm[key]);
      });

      // 営業情報であれば
      if (report_contents_update.type === "sales") {

        if (!report_contents_update.free) {
          // 会社情報を更新
          report_contents_update["client"] = this.clients.find(client => client.id === report_contents_update.client_id);

          // 営業所情報を更新
          report_contents_update["branch"] = this.branches.find(branch => branch.id === report_contents_update.branch_id);


          // 担当者が選択されていれば担当者情報を追加
          if (this.reportContentForm.contact_person_id) {

            console.log(this.contact_persons);
            console.log(this.reportContentForm.contact_person_id);

            report_contents_update["contact_persons"] = this.contact_persons.filter(contact_person => this.reportContentForm.contact_person_id.includes(contact_person.id));
          }
        }


        // 営業手段情報を更新
        report_contents_update["sales_method"] = this.sales_methods.find(sales_method => sales_method.id === report_contents_update.sales_method_id);
      }

      console.log('report_contents_update');
      console.log(report_contents_update);

      // 追加ダイアログフォーム・インデックス値をリセットして閉じる
      this.reportContentForm = {};
      this.reportContentEditingIndex = null;
      this.reportContentMode = null;
      this.dialog = false;
    },

    // 日報コンテンツ削除
    reportContentDelete: function (index) {
      this.$confirm('この報告を削除してよろしいですか？<br>削除した項目を元に戻すことはできません').then(isAccept => {
        if (isAccept) {
          // 削除
          delete this.form.report_contents.splice(index, 1)
        }
      })
    },

    // 日報作成実行
    create: function (draft_flg) {

      console.log(this.form.file_name);
      console.log(draft_flg);

      this.form
        .transform((data) => {
          // 日報コンテンツ情報をディープコピー
          let report_contents = _.cloneDeep(data["report_contents"]);

          report_contents.forEach((report_content) => {
            // 評価を配列に変更
            report_content["product_evaluation"]
              = Object.entries(report_content["product_evaluation"])
                .filter(([product_id, evaluation_id]) => {
                  return !!(evaluation_id)
                })
                .map(([product_id, evaluation_id]) => {
                  return { 'product_id': Number(product_id), 'evaluation_id': Number(evaluation_id) };
                });

            // 表示にのみ使用している会社情報を送信する内容から削除
            delete report_content.client;
            delete report_content.sales_method;
          });

          console.log(data);

          let report_attach_file = this.reportAttachFileContentDisplay;


          return {
            ...data,
            report_contents: report_contents,
            report_attach_file: report_attach_file,
            draft_flg: draft_flg,
            time: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(11, 5)
          }

        }
        )
        .post(this.$route('reports.store'), {

          onStart: () => this.$set(this.loading, "create", true),
          onSuccess: () => this.$toasted.show('日報を作成しました'),
          onError: errors => {
            // バリデーションエラーをトースト表示する
            // フロント側チェックを行っているため発生しない前提
            this.$toasted.error(Object.values(errors).join("\n"), {
              duration: 1000 * 60 * 10,
              type: 'error'
            });
          },
          onFinish: () => this.$set(this.loading, "create", false),
        })
    },
    // 下書き登録
    create2: function (draft_flg) {

      this.form
        .transform((data) => {
          // 日報コンテンツ情報をディープコピー
          let report_contents = _.cloneDeep(data["report_contents"]);

          report_contents.forEach((report_content) => {
            // 評価を配列に変更
            report_content["product_evaluation"]
              = Object.entries(report_content["product_evaluation"])
                .filter(([product_id, evaluation_id]) => {
                  return !!(evaluation_id)
                })
                .map(([product_id, evaluation_id]) => {
                  return { 'product_id': Number(product_id), 'evaluation_id': Number(evaluation_id) };
                });

            // 表示にのみ使用している会社情報を送信する内容から削除
            delete report_content.client;
            delete report_content.sales_method;
          });

          console.log(data);

          let report_attach_file = this.reportAttachFileContentDisplay;

          return {
            ...data,
            report_contents: report_contents,
            report_attach_file: report_attach_file,
            draft_flg: draft_flg,
            time: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(11, 5)
          }

        }
        )
        .post(this.$route('reports.store'), {

          onStart: () => this.$set(this.loading, "create2", true),
          onSuccess: () => this.$toasted.show('日報を作成しました'),
          onError: errors => {
            // バリデーションエラーをトースト表示する
            // フロント側チェックを行っているため発生しない前提
            this.$toasted.error(Object.values(errors).join("\n"), {
              duration: 1000 * 60 * 10,
              type: 'error'
            });
          },
          onFinish: () => this.$set(this.loading, "create2", false),
        })
    },

    //日報添付ファイルモーダル表示
    openReportFileModal: function (index) {

      //日報添付ファイル
      this.reportAttachFileContent = [];

      this.reportFileDialog = true;
    },

    closeReportDialog() {
      this.reportFileDialog = false;
      this.uploadFlg = true;
    },

    upcountReportFile: function () {
      this.reportAttachFileCount++;
    },

    //日報添付ファイルアップロード処理
    uploadReportAttachFile: function (e) {

      this.uploadFlg = false;

      console.log('uploadReportAttachFile');
      console.log(e);
      console.log(e.target);

      var file = e.target.files[0];
      var index = e.target.getAttribute('data-id')

      console.log(file);
      console.log(index);

      const config = {
        header: {
          "Content-Type": "multipart/form-data"
        }
      };

      var formData = new FormData();

      var type = 'image'
      if (!file.type.includes('image')) {
        type = 'file';
      }

      //appendの第一引数がkey、第二引数がデータ
      formData.append("file", file);

      axios.post("/report-file/upload", formData, config).then(res => {
        var tmp = [];
        tmp['name'] = res.data.name;
        tmp['original_name'] = res.data.original_name;
        tmp['type'] = type;


        this.reportAttachFileContent[index] = tmp;
        this.uploadFlg = true;


      }).catch(err => {
        alert('ファイルのアップに失敗しました。もう一度実施お願いします。');
        this.uploadFlg = true;
      });
    },



    //日報添付ファイルの名称受け渡し
    addReportAttachFile: function () {

      var tmpReportAttachFile = [];

      //配列カウント
      var index_count = 0;

      //すでに存在している場合
      if (this.reportAttachFileContentDisplay.length > 0) {
        this.reportAttachFileContentDisplay.forEach(function (data, index) {
          tmpReportAttachFile[index_count] = data;
          index_count++;
        });
      }

      console.log('tmpReportAttachFile');
      console.log('現状のものを追記');
      console.log(index_count);

      // フォームに送る値を書き換える
      this.reportAttachFileContent.forEach(function (file) {
        if (file) {

          var reportAttachContentFile = {
            name: file.name,
            original_name: file.original_name,
            type: file.type,
          };

          tmpReportAttachFile[index_count] = reportAttachContentFile;
          index_count++;
        }
      });

      console.log('tmpReportAttachFile');
      console.log(tmpReportAttachFile);
      console.log(index_count);

      this.reportAttachFileContentDisplay = tmpReportAttachFile;
      this.reportAttachFileContent = [];
      this.reportAttachFileCount = 3;


      console.log(this.reportAttachFileContentDisplay);

      this.reportFileDialog = false;

    },


    clickDeleteReportAttachFile(index) {
      this.$confirm('この写真を削除してよろしいですか？<br>削除した項目を元に戻すことはできません').then(isAccept => {
        if (isAccept) {
          // 削除
          delete this.reportAttachFileContentDisplay.splice(index, 1)
        }
      })
    },


  }
}
</script>
