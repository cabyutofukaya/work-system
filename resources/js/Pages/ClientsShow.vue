<template xmlns="http://www.w3.org/1999/html">
  <Layout>
    <v-card flat tile>
      <v-card-title>
        <v-icon dark left v-html="client['client_type_icon']"></v-icon>
        {{ client["client_type_name"] }}
      </v-card-title>

      <v-card-text>
        <v-row>
          <v-col class="text-right">
            <Link as="Button" :to="$route('clients.edit', { client: client.id })" :small="$vuetify.breakpoint.xs"
              class="ml-2">
            <v-icon left>
              mdi-pencil
            </v-icon>
            編集
            </Link>

            <Link as="Button" :small="$vuetify.breakpoint.xs" class="ml-2" color="error" @click.native="deleteClient"
              :loading="loading['delete']">
            <v-icon left>
              mdi-delete-outline
            </v-icon>
            削除
            </Link>
          </v-col>
        </v-row>

        <div class="description-list">
          <!-- <v-row>
            <v-col cols="12" sm="4">会社名</v-col>
            <v-col>
              <div class="d-flex align-center">
                <div>
                  <v-list-item-avatar tile @click.prevent="imageOverlayUrl = client['image_url']">
                    <v-img :src="client['image_url']" alt=""></v-img>
                  </v-list-item-avatar>
                </div>

                <div>
                  <div>
                    {{ client.name }}
                  </div>
                </div>
              </div>
            </v-col>
          </v-row> -->

          <v-row>
            <v-col cols="12" sm="4">会社名</v-col>
            <v-col>
              {{ client.name }}
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" sm="4">更新日時</v-col>
            <v-col>
              {{ client.updated_at }}
            </v-col>
          </v-row>

          <v-row v-if="client.name_kana">
            <v-col cols="12" sm="4">会社名よみがな</v-col>
            <v-col>{{ client.name_kana }}</v-col>
          </v-row>

          <v-row>
            <v-col cols="12" sm="4">所在地</v-col>
            <v-col>
              {{ client.postcode }}<br>
              <a :href="googleMapUrl" target="_blank">
                {{ client.prefecture }}{{ client.address }}

                <v-icon small>
                  mdi-open-in-new
                </v-icon>
              </a>
            </v-col>
          </v-row>

          <v-row v-if="client.url">
            <v-col cols="12" sm="4">URL</v-col>
            <v-col>
              <a :href="client.url">{{ client.url }}</a>
            </v-col>
          </v-row>

          <v-row v-if="client.email">
            <v-col cols="12" sm="4">メールアドレス</v-col>
            <v-col>
              <a :href="'mailto:' + client.email">{{ client.email }}</a>
            </v-col>
          </v-row>

          <v-row v-if="client.representative">
            <v-col cols="12" sm="4">代表者名</v-col>
            <v-col>{{ client.representative }}</v-col>
          </v-row>

          <v-row v-if="client.representative_kana">
            <v-col cols="12" sm="4">代表者カナ</v-col>
            <v-col>{{ client.representative_kana }}</v-col>
          </v-row>

          <v-row v-if="client.representative_position">
            <v-col cols="12" sm="4">代表者役職</v-col>
            <v-col>{{ client.representative_position }}</v-col>
          </v-row>


          <v-row v-if="client.tel">
            <v-col cols="12" sm="4">電話番号</v-col>
            <v-col><a :href="'tel:' + client.tel">{{ client.tel }}</a></v-col>
          </v-row>

          <v-row v-if="client.fax">
            <v-col cols="12" sm="4">FAX番号</v-col>
            <v-col><a :href="'tel:' + client.fax">{{ client.fax }}</a></v-col>
          </v-row>

          <!-- 営業エリア -->
          <v-row v-if="client['client_type_id'] === 'taxibus'">
            <v-col cols="12" sm="4">営業エリア</v-col>
            <v-col>
              <v-row v-for="business_district in client['business_districts']" :key="business_district['id']">
                <v-col>
                  {{ business_district["prefecture"] }}{{ business_district["address"] }}
                </v-col>

                <v-col cols="auto">
                  <Link as="Button" :small="$vuetify.breakpoint.xs"
                    @click.native="openUpdateBusinessDistrictDialog(business_district)" dusk="businessDistrictEdit">
                  <v-icon left>
                    mdi-pencil
                  </v-icon>
                  編集
                  </Link>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="" v-if="!client['business_districts'].length">
                  営業エリアは設定されていません
                </v-col>

                <v-col cols="12" sm="" class="text-right">
                  <Link as="Button" :small="$vuetify.breakpoint.xs" @click.native="openCreateBusinessDistrictDialog()">
                  <v-icon left>
                    mdi-plus
                  </v-icon>
                  営業エリアを追加する
                  </Link>
                </v-col>
              </v-row>
            </v-col>
          </v-row>

          <!-- 営業所 -->
          <v-row id="branches">
            <v-col cols="12" sm="4">営業所</v-col>
            <v-col>
              <v-row v-for="branch in client['branches']" :key="branch.id">
                <v-col class="mr-auto">
                  {{ branch["name"] }}<br>
                  <template v-if="branch['postcode']">{{ branch["postcode"] }}<br></template>
                  <template v-if="branch['prefecture'] || branch['address']">
                    <a :href="'https://www.google.com/maps/search/?api=1&query=' + branch.lat + ',' + branch.lng"
                      target="_blank">
                      {{ branch["prefecture"] }}{{ branch["address"] }}

                      <v-icon small>
                        mdi-open-in-new
                      </v-icon>
                    </a><br>
                  </template>
                  <template v-if="branch['tel']">TEL:<a :href="'tel:' + branch['tel']">{{ branch["tel"] }}</a></template>
                  <template v-if="branch['fax']">FAX:<a :href="'tel:' + branch['fax']">{{ branch["fax"] }}</a></template>
                </v-col>

                <v-col cols="auto" class="text-right">
                  <Link as="Button" class="mr-2" :small="$vuetify.breakpoint.xs"
                    :to="$route('branches.edit', { branch: branch['id'] })" dusk="branchEdit">
                  <v-icon left>
                    mdi-pencil
                  </v-icon>
                  編集
                  </Link>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="" v-if="!client['branches'].length">
                  営業所は設定されていません
                </v-col>

                <v-col cols="12" sm="" class="text-right">
                  <Link as="Button" :small="$vuetify.breakpoint.xs"
                    :to="$route('clients.branches.create', { client: client.id })">
                  <v-icon left>
                    mdi-plus
                  </v-icon>
                  営業所を追加する
                  </Link>
                </v-col>
              </v-row>
            </v-col>
          </v-row>

          <v-row v-if="client['business_hours']">
            <v-col cols="12" sm="4">営業時間</v-col>
            <v-col>{{ client["business_hours"] }}</v-col>
          </v-row>

          <!-- 固有情報 -->
          <template v-if="client['client_type_taxibus']">
            <v-row v-if="client['client_type_taxibus']['membership_fee'] !== null">
              <v-col cols="12" sm="4">会費</v-col>
              <v-col>{{ client["client_type_taxibus"]["membership_fee"] | currency }}</v-col>
            </v-row>

            <v-row
              v-if="client['client_type_taxibus']['fee_taxi_cab'] !== null || client['client_type_taxibus']['fee_taxi_tabinoashi'] !== null || client['client_type_taxibus']['fee_bus_cab'] !== null || client['client_type_taxibus']['fee_bus_tabinoashi'] !== null">
              <v-col cols="12" sm="4">手数料</v-col>
              <v-col>
                <template
                  v-if="client['client_type_taxibus']['fee_taxi_cab'] !== null || client['client_type_taxibus']['fee_taxi_tabinoashi'] !== null">
                  <div>
                    タクシー
                  </div>

                  <v-chip small outlined class="mr-2 my-2" color="primary"
                    v-if="client['client_type_taxibus']['fee_taxi_cab'] !== null" :value="true">
                    CAB {{ client["client_type_taxibus"]["fee_taxi_cab"] | currency }}
                  </v-chip>

                  <v-chip small outlined class="mr-2 my-2" color="primary"
                    v-if="client['client_type_taxibus']['fee_taxi_tabinoashi'] !== null" :value="true">
                    たびの足 {{ client["client_type_taxibus"]["fee_taxi_tabinoashi"] | currency }}
                  </v-chip>
                </template>

                <template
                  v-if="client['client_type_taxibus']['fee_bus_cab'] !== null || client['client_type_taxibus']['fee_bus_tabinoashi'] !== null">
                  <div>
                    バス
                  </div>

                  <v-chip small outlined class="mr-2 my-2" color="primary"
                    v-if="client['client_type_taxibus']['fee_bus_cab'] !== null" :value="true">
                    CAB {{ client["client_type_taxibus"]["fee_bus_cab"] | currency }}
                  </v-chip>

                  <v-chip small outlined class="mr-2 my-2" color="primary"
                    v-if="client['client_type_taxibus']['fee_bus_tabinoashi'] !== null" :value="true">
                    たびの足 {{ client["client_type_taxibus"]["fee_bus_tabinoashi"] | currency }}
                  </v-chip>
                </template>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">カテゴリー</v-col>
              <v-col>
                {{ client["client_type_taxibus"]["category_name"] }}
              </v-col>
            </v-row>
          </template>

          <!-- 担当者 -->
          <v-row>
            <v-col cols="12" sm="4">担当者</v-col>
            <v-col>
              <v-row v-for="contact_person in client['contact_persons']" :key="contact_person['id']">
                <v-col>
                  名前:{{ contact_person["name"] }}<br>
                  <span v-if="contact_person['email']">
                    <a :href="'mailto:' + contact_person['email']">{{ contact_person["email"] }}</a><br>
                  </span>
                  <span v-if="contact_person['tel']">
                    <template v-if="contact_person['tel']"><a :href="'tel:' + contact_person['tel']">{{
                      contact_person["tel"] }}</a><br></template>
                  </span>
                  所属部署:{{ contact_person["department"] }}/ 役職:{{ contact_person["position"] }}
                </v-col>
                <v-col cols="auto" class="text-right">
                  <Link as="Button" :small="$vuetify.breakpoint.xs"
                    @click.native="openUpdateContactPersonDialog(contact_person)" dusk="contactPersonEdit">
                  <v-icon left>
                    mdi-pencil
                  </v-icon>
                  編集
                  </Link>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12" sm="" v-if="!client['contact_persons'].length">
                  担当者は設定されていません
                </v-col>

                <v-col cols="12" sm="" class="text-right">
                  <Link as="Button" :small="$vuetify.breakpoint.xs" @click.native="openCreateContactPersonDialog()">
                  <v-icon left>
                    mdi-plus
                  </v-icon>
                  担当者を追加する
                  </Link>
                </v-col>
              </v-row>
            </v-col>
          </v-row>

          <!-- 固有情報 バス・タクシー会社 -->
          <template v-if="client['client_type_taxibus']">
            <v-row>
              <v-col cols="12" sm="4">DR</v-col>
              <v-col>
                <Chip :value="client['client_type_taxibus']['has_dr_sightseeing']">
                  観光DR
                </Chip>

                <Chip :value="client['client_type_taxibus']['has_dr_female']">
                  女性DR
                </Chip>

                <Chip :value="client['client_type_taxibus']['has_dr_language_english']">
                  英語DR
                </Chip>

                <Chip :value="client['client_type_taxibus']['has_dr_language_chinese']">
                  中国語DR
                </Chip>

                <Chip :value="client['client_type_taxibus']['has_dr_language_korean']">
                  韓国語DR
                </Chip>

                <Chip :value="client['client_type_taxibus']['has_dr_language_other']">
                  他言語DR
                </Chip>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">車椅子</v-col>
              <v-col>
                <Chip :value="client['client_type_taxibus']['has_wheelchair']">
                  {{ client["client_type_taxibus"]["has_wheelchair"] ? "有り" : "無し" }}
                </Chip>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">シート</v-col>
              <v-col>
                <Chip :value="client['client_type_taxibus']['has_baby_seat']">
                  ベビーシート
                </Chip>

                <template v-if="client['client_type_taxibus']['has_child_seat']">
                  <Chip :value="true">
                    チャイルドシート&nbsp;
                    <span v-if="client['client_type_taxibus']['fee_child_seat']">{{
                      client["client_type_taxibus"]["fee_child_seat"] | currency }}</span>
                    <span v-else>無料</span>
                  </Chip>
                </template>
                <template v-else>
                  <Chip :value="false">
                    チャイルドシート
                  </Chip>
                </template>

                <template v-if="client['client_type_taxibus']['has_junior_seat']">
                  <Chip :value="true">
                    ジュニアシート&nbsp;
                    <span v-if="client['client_type_taxibus']['fee_junior_seat']">{{
                      client["client_type_taxibus"]["fee_junior_seat"] | currency }}</span>
                    <span v-else>無料</span>
                  </Chip>
                </template>
                <template v-else>
                  <Chip :value="false">
                    ジュニアシート
                  </Chip>
                </template>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">バス協加盟</v-col>
              <v-col>
                <Chip :value="client['client_type_taxibus']['is_bus_association_member']">
                  {{ client["client_type_taxibus"]["has_baby_seat"] ? "有り" : "無し" }}
                </Chip>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="4">セーフティーマーク</v-col>
              <v-col>
                <Chip :value="client['client_type_taxibus']['has_safety_mark']">
                  {{ client["client_type_taxibus"]["has_baby_seat"] ? "有り" : "無し" }}
                </Chip>
              </v-col>
            </v-row>

            <v-row id="vehicles-taxi">
              <v-col cols="12" sm="4">保有車両 タクシー</v-col>
              <v-col>
                <v-list dense v-if="client['vehicles_taxi'].length">
                  <Link as="v-list-item" v-for="vehicle_taxi in client['vehicles_taxi']" :key="vehicle_taxi['id']"
                    :href="$route('vehicles.show', { vehicle: vehicle_taxi['id'] })" exact dusk="vehicleTaxiShow">
                  <v-list-item-avatar tile size="80">
                    <v-img :src="vehicle_taxi['icon_image_url']" alt=""></v-img>
                  </v-list-item-avatar>

                  <v-list-item-title>
                    {{ vehicle_taxi["name"] }}
                  </v-list-item-title>
                  </Link>
                </v-list>

                <v-row>
                  <v-col cols="12" sm="" v-if="!client['vehicles_taxi'].length">
                    タクシーは設定されていません
                  </v-col>

                  <v-col cols="12" sm="" class="text-right">
                    <Button :small="$vuetify.breakpoint.xs"
                      :to="$route('clients.vehicles.create', { client: client.id, _query: { type: 'taxi' } })">
                      <v-icon left>
                        mdi-plus
                      </v-icon>
                      タクシーを追加する
                    </Button>
                  </v-col>
                </v-row>
              </v-col>
            </v-row>

            <v-row id="vehicles-bus">
              <v-col cols="12" sm="4">保有車両 バス</v-col>
              <v-col>
                <v-list dense v-if="client['vehicles_bus'].length">
                  <Link as="v-list-item" v-for="vehicle_bus in client['vehicles_bus']" :key="vehicle_bus['id']"
                    :href="$route('vehicles.show', { vehicle: vehicle_bus['id'] })" exact dusk="vehicleBusShow">
                  <v-list-item-avatar tile size="80">
                    <v-img :src="vehicle_bus['icon_image_url']" alt=""></v-img>
                  </v-list-item-avatar>

                  <v-list-item-title>
                    {{ vehicle_bus["name"] }}
                  </v-list-item-title>
                  </Link>
                </v-list>

                <v-row>
                  <v-col cols="12" sm="" v-if="!client['vehicles_bus'].length">
                    バスは設定されていません
                  </v-col>

                  <v-col cols="12" sm="" class="text-right">
                    <Button :small="$vuetify.breakpoint.xs"
                      :to="$route('clients.vehicles.create', { client: client.id, _query: { type: 'bus' } })">
                      <v-icon left>
                        mdi-plus
                      </v-icon>
                      バスを追加する
                    </Button>
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </template>

          <!-- 固有情報 トラック会社 -->
          <template v-if="client['client_type_truck']">
            <v-row v-if="client['client_type_truck']['drivers_count']">
              <v-col cols="12" sm="4">ドライバー数</v-col>
              <v-col>
                {{ client["client_type_truck"]["drivers_count"] }}
              </v-col>
            </v-row>
          </template>

          <!-- 固有情報 飲食店 -->
          <template v-if="client['client_type_restaurant']">
            <v-row v-if="client['client_type_restaurant']['languages']?.length">
              <v-col cols="12" sm="4">言語</v-col>
              <v-col>
                <v-chip small class="mr-2 mb-2" v-for="(language, index) in client['client_type_restaurant']['languages']"
                  :key="index">
                  {{ language }}
                </v-chip>
              </v-col>
            </v-row>
          </template>

          <!-- 固有情報 旅行業者 -->
          <template v-if="client['client_type_travel']">
            <v-row v-if="client['client_type_travel']['payment_method']">
              <v-col cols="12" sm="4">支払い方法</v-col>
              <v-col>
                {{ client["client_type_travel"]["payment_method"] }}
              </v-col>
            </v-row>

            <v-row v-if="client['client_type_travel']['registration_number']">
              <v-col cols="12" sm="4">旅行業登録番号</v-col>
              <v-col>
                {{ client["client_type_travel"]["registration_number"] }}
              </v-col>
            </v-row>

            <v-row v-if="client['client_type_travel']['group']">
              <v-col cols="12" sm="4">JATA/ANTA/その他</v-col>
              <v-col>
                {{ client["client_type_travel"]["group"] }}
              </v-col>
            </v-row>
          </template>

          <!-- ジャンル -->
          <v-row v-if="client['genres'].length">
            <v-col cols="12" sm="4">ジャンル</v-col>
            <v-col>
              <v-chip small class="mr-2 mb-2" v-for="genre in client['genres']" :key="genre.id">
                {{ genre.name }}
              </v-chip>
            </v-col>
          </v-row>

          <!-- 商材 -->
          <v-row v-if="client['products'].length">
            <v-col cols="12" sm="4">商材</v-col>
            <v-col>
              <v-chip small class="mr-2 mb-2" v-for="product in client['products']" :key="product.id">
                {{ product.name }}
              </v-chip>
            </v-col>
          </v-row>

          <!-- 社内担当者 -->
          <v-row v-if="client['users'].length">
            <v-col cols="12" sm="4">社内担当者</v-col>
            <v-col>
              <v-chip small class="mr-2 mb-2" v-for="user in client['users']" :key="user.id">
                {{ user.name }}
              </v-chip>
            </v-col>
          </v-row>

          <!-- 直近の営業ToDo -->
          <v-row>
            <v-col cols="12" sm="4">直近の営業ToDo</v-col>
            <v-col>
              <v-list three-line dense class="pa-0" v-if="sales_todos.length">
                <div v-for="(sales_todo, index) in sales_todos" :key="index">
                  <v-divider v-if="index !== 0"></v-divider>

                  <v-list-item exact>
                    <v-list-item-content>
                      <v-list-item-title>
                        相手先担当者：{{ sales_todo.contact_person }}
                      </v-list-item-title>
                      <v-list-item-subtitle>
                        {{ sales_todo.scheduled_at }}
                      </v-list-item-subtitle>
                      <v-list-item-subtitle class="overflow-hidden">
                        <div class="text-nowrap overflow-hidden">
                          <span style="white-space: pre-line;">{{ sales_todo.description }}</span>
                        </div>
                      </v-list-item-subtitle>
                    </v-list-item-content>
                  </v-list-item>
                </div>
              </v-list>

              <v-row>
                <v-col cols="12" sm="" v-if="!sales_todos.length">
                  まだ営業ToDoはありません
                </v-col>

                <v-col cols="12" sm="" class="text-right">
                  <Link as="Button" class="ml-2 mb-1" :small="$vuetify.breakpoint.xs"
                    :to="$route('sales-todos.index', { _query: { client_id: client.id } })" v-if="sales_todos.length">
                  <v-icon>
                    mdi-format-list-bulleted
                  </v-icon>
                  この会社のすべての営業ToDoを見る
                  </Link>

                  <Link as="Button" class="ml-2 mb-1" :small="$vuetify.breakpoint.xs"
                    :to="$route('sales-todos.create', { _query: { client_id: client.id } })">
                  <v-icon left>
                    mdi-plus
                  </v-icon>
                  この会社のToDoを追加する
                  </Link>
                </v-col>
              </v-row>
            </v-col>
          </v-row>

          <!-- 最近の営業日報 -->
          <v-row>
            <v-col cols="12" sm="4">最近の営業日報</v-col>
            <v-col>
              <v-list three-line dense class="pa-0" v-if="report_contents.length">
                <div v-for="(report_content, index) in report_contents" :key="index">
                  <v-divider v-if="index !== 0"></v-divider>
                  <Link as="v-list-item" three-line :href="$route('reports.show', { report: report_content.report_id })"
                    dusk="reportShow">
                  <v-list-item-content>
                    <v-list-item-title>
                      {{ report_content.report.user.name }}
                    </v-list-item-title>
                    <v-list-item-subtitle>
                      {{ report_content.report.date }}
                    </v-list-item-subtitle>
                    <v-list-item-subtitle class="overflow-hidden">
                      <div class="text-nowrap overflow-hidden">
                        {{ report_content.description }}
                      </div>
                    </v-list-item-subtitle>
                  </v-list-item-content>
                  </Link>
                </div>
              </v-list>

              <v-row>
                <v-col cols="12" sm="" v-if="!report_contents.length">
                  まだ営業日報はありません
                </v-col>

                <v-col cols="12" sm="" class="text-right">
                  <Link as="Button" class="ml-2 mb-1" :small="$vuetify.breakpoint.xs"
                    :to="$route('reports.index', { _query: { client_id: client.id } })" v-if="report_contents.length">
                  <v-icon>
                    mdi-format-list-bulleted
                  </v-icon>
                  この会社のすべての営業日報を見る
                  </Link>

                  <Link as="Button" class="ml-2 mb-1" :small="$vuetify.breakpoint.xs"
                    :to="$route('reports.create', { _query: { client_id: client.id } })">
                  <v-icon left>
                    mdi-plus
                  </v-icon>
                  この会社の営業日報を作成する
                  </Link>
                </v-col>
              </v-row>
            </v-col>
          </v-row>

          <!-- 会社訪問状態 -->
          <v-row v-if="client['contact']">
            <v-col cols="12" sm="4">問い合わせ経路</v-col>
            <v-col>
              {{ client['contact'] }}
            </v-col>
          </v-row>

          <!-- 7月始まり年度別の訪問回数(営業日報数) -->
          <v-row>
            <v-col cols="12" sm="4">訪問回数</v-col>
            <v-col>
              <template v-if="report_contents_count_by_fy.length">
                この会社へのあなたの訪問(7月開始の年度別)<br>
                <div v-for="report_content in report_contents_count_by_fy" :key="report_content.fiscal_year">
                  {{ report_content.fiscal_year }}年度 {{ report_content.count }}件<br>
                </div>
              </template>
              <template v-else>
                この会社へのあなたの訪問はありません
              </template>
            </v-col>
          </v-row>

          <!-- 商材の評価 -->
          <v-row>
            <v-col cols="12" sm="4">商材の評価</v-col>
            <v-col>
              <v-list dense class="pa-0" v-if="latest_evaluations.length">
                <!-- ラベル行 PCビューのみ -->
                <v-list-item v-if="$vuetify.breakpoint.smAndUp">
                  <v-row class="">
                    <v-col cols="3">商材名</v-col>
                    <v-col cols="2">最新の評価</v-col>
                    <v-col cols="2">日付</v-col>
                    <v-col cols="5">日報作成者</v-col>
                  </v-row>
                </v-list-item>

                <div v-for="(latest_evaluation, index) in latest_evaluations" :key="index">
                  <v-divider></v-divider>

                  <Link as="v-list-item" three-line
                    :href="$route('reports.show', { report: latest_evaluation['report_content']['report_id'] })">
                  <!-- PCビュー -->
                  <v-row v-if="!$vuetify.breakpoint.xs">
                    <v-col cols="3">{{ latest_evaluation['product']['name'] }}</v-col>
                    <v-col cols="2">
                      <EvaluationIcon :evaluation="latest_evaluation['evaluation']['grade']"></EvaluationIcon>
                      {{ latest_evaluation['evaluation']['label'] }}
                    </v-col>
                    <v-col cols="2">{{ latest_evaluation['report_content']['report']['date'] }}</v-col>
                    <v-col cols="5">{{ latest_evaluation['report_content']['report']['user']['name'] }}</v-col>
                  </v-row>

                  <!-- スマートフォンビュー -->
                  <v-row v-else justify="center">
                    <v-col cols="10">
                      {{ latest_evaluation['product']['name'] }}<br>
                      {{ latest_evaluation['report_content']['report']['date'] }} {{
                        latest_evaluation['report_content']['report']['user']['name'] }}
                    </v-col>
                    <v-col cols="2">
                      <EvaluationIcon class="mt-2" :evaluation="latest_evaluation['evaluation']['grade']">
                      </EvaluationIcon>
                    </v-col>
                  </v-row>
                  </Link>
                </div>
              </v-list>

              <template v-if="latest_evaluations.length">
                この会社の営業日報に設定された各商材の評価のうち、日付が最も新しい日報の評価を最新の評価として表示しています。
              </template>

              <template v-if="!latest_evaluations.length">
                まだ商材の評価はありません
              </template>
            </v-col>
          </v-row>

          <v-row v-if="client['description']">
            <v-col cols="12" sm="4">説明</v-col>
            <v-col>
              <span style="white-space: pre-line;">{{ client.description }}</span>
            </v-col>
          </v-row>
        </div>
      </v-card-text>

      <v-divider></v-divider>

      <v-card-text class="text-right">
        <BackButton></BackButton>
      </v-card-text>
    </v-card>

    <!-- 担当者編集ダイアログ -->
    <v-dialog v-model="contactPersonDialog" :max-width="$vuetify.breakpoint.smAndUp ? '600px' : 'unset'"
      @click:outside="contactPersonForm.clearErrors()">
      <v-card flat tile>
        <v-card-title>
          <template v-if="this.contactPersonDialogMode === 'update'">
            担当者の編集
          </template>
          <template v-else>
            担当者の作成
          </template>
        </v-card-title>

        <v-card-text>
          <v-list>
            <v-list-item>
              <v-text-field dense filled prepend-inner-icon="mdi-pencil" label="名前" name="name" maxlength="200"
                v-model="contactPersonForm.name" :error="Boolean(contactPersonForm.errors.name)"
                :error-messages="contactPersonForm.errors.name"></v-text-field>
            </v-list-item>

            <v-list-item>
              <v-text-field dense filled prepend-inner-icon="mdi-pencil" label="メールアドレス" name="email"
                v-model="contactPersonForm.email" :error="Boolean(contactPersonForm.errors.email)"
                :error-messages="contactPersonForm.errors.email"></v-text-field>
            </v-list-item>

            <v-list-item>
              <v-text-field dense filled prepend-inner-icon="mdi-pencil" label="電話番号" name="tel"
                v-model="contactPersonForm.tel" :error="Boolean(contactPersonForm.errors.tel)"
                :error-messages="contactPersonForm.errors.tel"></v-text-field>
            </v-list-item>

            <v-list-item>
              <v-text-field dense filled prepend-inner-icon="mdi-pencil" label="所属部署" name="department"
                v-model="contactPersonForm.department" :error="Boolean(contactPersonForm.errors.department)"
                :error-messages="contactPersonForm.errors.department"></v-text-field>
            </v-list-item>

            <v-list-item>
              <v-text-field dense filled prepend-inner-icon="mdi-pencil" label="役職" name="position"
                v-model="contactPersonForm.position" :error="Boolean(contactPersonForm.errors.position)"
                :error-messages="contactPersonForm.errors.position"></v-text-field>
            </v-list-item>
          </v-list>
        </v-card-text>

        <v-card-text class="text-center">
          <Link as="Button" :small="$vuetify.breakpoint.xs" color="primary"
            v-if="this.contactPersonDialogMode === 'update'" @click.native="updateContactPerson"
            :loading="loading['contact-person-update']">
          <v-icon left>
            mdi-content-save-edit-outline
          </v-icon>
          この内容で更新する
          </Link>
          <Link as="Button" :small="$vuetify.breakpoint.xs" color="primary"
            v-if="this.contactPersonDialogMode === 'store'" @click.native="createContactPerson"
            :loading="loading['contact-person-create']">
          <v-icon left>
            mdi-content-save-outline
          </v-icon>
          この内容で作成する
          </Link>
        </v-card-text>

        <v-card-text class="text-center">
          <Link as="Button" :small="$vuetify.breakpoint.xs" color="error" v-if="contactPersonDialogMode === 'update'"
            @click.native="deleteContactPerson" :loading="loading['contact-person-delete']">
          <v-icon left>
            mdi-delete-outline
          </v-icon>
          この担当者を削除する
          </Link>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-text class="text-right">
          <Button class="mt-4" :small="$vuetify.breakpoint.xs"
            @click.native="contactPersonForm.clearErrors(); contactPersonDialog = false">
            <v-icon>
              mdi-close
            </v-icon>
            閉じる
          </Button>
        </v-card-text>
      </v-card>
    </v-dialog>

    <!-- 営業エリア編集ダイアログ -->
    <v-dialog v-model="businessDistrictDialog" :max-width="$vuetify.breakpoint.smAndUp ? '600px' : 'unset'"
      @click:outside="businessDistrictForm.clearErrors()">
      <v-card flat tile>
        <v-card-title>
          <template v-if="this.businessDistrictDialogMode === 'update'">
            営業エリアの編集
          </template>
          <template v-else>
            営業エリアの作成
          </template>
        </v-card-title>

        <v-card-text>
          <v-list>
            <v-list-item>
              <v-select dense filled label="都道府県" :items="prefectures" name="prefecture"
                v-model="businessDistrictForm.prefecture" :error="Boolean(businessDistrictForm.errors.prefecture)"
                :error-messages="businessDistrictForm.errors.prefecture">
              </v-select>
            </v-list-item>

            <v-list-item>
              <v-text-field dense filled prepend-inner-icon="mdi-pencil" label="市区町村" name="address"
                v-model="businessDistrictForm.address" :error="Boolean(businessDistrictForm.errors.address)"
                :error-messages="businessDistrictForm.errors.address"></v-text-field>
            </v-list-item>
          </v-list>
        </v-card-text>

        <v-card-text class="text-center">
          <Link as="Button" :small="$vuetify.breakpoint.xs" color="primary"
            v-if="this.businessDistrictDialogMode === 'update'" @click.native="updateBusinessDistrict"
            :loading="loading['business-district-update']">
          <v-icon left>
            mdi-content-save-edit-outline
          </v-icon>
          この内容で更新する
          </Link>
          <Link as="Button" :small="$vuetify.breakpoint.xs" color="primary"
            v-if="this.businessDistrictDialogMode === 'store'" @click.native="createBusinessDistrict"
            :loading="loading['business-district-create']">
          <v-icon left>
            mdi-content-save-outline
          </v-icon>
          この内容で作成する
          </Link>
        </v-card-text>

        <v-card-text class="text-center">
          <Link as="Button" :small="$vuetify.breakpoint.xs" color="error" v-if="businessDistrictDialogMode === 'update'"
            @click.native="deleteBusinessDistrict" :loading="loading['business-district-delete']">
          <v-icon left>
            mdi-delete-outline
          </v-icon>
          この営業エリアを削除する
          </Link>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-text class="text-right">
          <Button class="mt-4" :small="$vuetify.breakpoint.xs"
            @click.native="businessDistrictForm.clearErrors(); businessDistrictDialog = false">
            <v-icon>
              mdi-close
            </v-icon>
            閉じる
          </Button>
        </v-card-text>
      </v-card>
    </v-dialog>

    <!-- イメージオーバーレイ表示 -->
    <ImageOverlay v-bind:url.sync="imageOverlayUrl"></ImageOverlay>
  </Layout>
</template>

<script>
import Layout from "./Layout";
import { Link } from "@inertiajs/inertia-vue";

export default {
  components: { Layout, Link },

  props: ['prefectures', 'sales_todos', 'report_contents', 'report_contents_count_by_fy', 'latest_evaluations', 'client', 'client_type_column_names'],

  data() {
    return {
      showContact: [],
      imageOverlayUrl: undefined,
      // 担当者
      contactPersonDialog: false,
      contactPersonDialogMode: null, // store|update
      contactPersonForm: this.$inertia.form({
        id: null,
        client_id: this.client.id,
        name: null,
        email: null,
        tel: null,
        department: null,
        position: null,
      }),
      // 営業エリア
      businessDistrictDialog: false,
      businessDistrictDialogMode: null, // store|update
      businessDistrictForm: this.$inertia.form({
        id: null,
        client_id: this.client.id,
        prefecture: null,
        address: null,
      }),
      loading: {}
    }
  },

  computed: {
    // GoogleMap 外部リンクURL
    googleMapUrl: function () {
      let url = "";

      if (this.client.lat && this.client.lng) {
        // 位置情報で指定
        url = 'https://www.google.com/maps/search/?api=1&query=' + this.client.lat + ',' + this.client.lng;
      } else if (this.client.prefecture || this.client.address) {
        // 位置情報がない場合は所在地を指定
        url = 'https://www.google.com/maps/search/?api=1&query=' + this.client.prefecture + this.client.address;
      }

      return url;
    }
  },

  methods: {
    // 会社削除
    deleteClient: function () {
      this.$confirm('この会社を削除してよろしいですか？<br>削除した項目を元に戻すことはできません').then(isAccept => {
        if (isAccept) {
          this.$inertia.delete(this.$route('clients.destroy', { client: this.client.id }), {
            onStart: () => this.$set(this.loading, "delete", true),
            onSuccess: () => this.$toasted.show('会社情報を削除しました'),
            onFinish: () => this.$set(this.loading, "delete", false),
          })
        }
      })
    },

    // 担当者更新ダイアログ
    openUpdateContactPersonDialog: function (contactPerson) {
      this.contactPersonDialog = true;
      this.contactPersonDialogMode = "update";

      this.contactPersonForm.id = contactPerson.id;
      this.contactPersonForm.name = contactPerson.name;
      this.contactPersonForm.email = contactPerson.email;
      this.contactPersonForm.tel = contactPerson.tel;
      this.contactPersonForm.department = contactPerson.department;
      this.contactPersonForm.position = contactPerson.position;
    },

    // 担当者更新
    updateContactPerson: function (contactPersonId) {
      this.contactPersonForm.put(this.$route('contact-persons.update', { contact_person: this.contactPersonForm.id }), {
        preserveScroll: true,
        onStart: () => this.$set(this.loading, "contact-person-update", true),
        onSuccess: () => {
          this.$toasted.show('担当者情報を更新しました');
          this.contactPersonDialog = false;
        },
        onFinish: () => this.$set(this.loading, "contact-person-update", false),
      })
    },

    // 担当者削除
    deleteContactPerson: function () {
      this.$confirm('この担当者を削除してよろしいですか？<br>削除した項目を元に戻すことはできません').then(isAccept => {
        if (isAccept) {
          this.contactPersonDialog = false;

          this.contactPersonForm.delete(this.$route('contact-persons.destroy', { contact_person: this.contactPersonForm.id }), {
            preserveScroll: true,
            onStart: () => this.$set(this.loading, "contact-person-delete", true),
            onSuccess: () => this.$toasted.show('担当者情報を削除しました'),
            onFinish: () => this.$set(this.loading, "contact-person-delete", false),
          });
        }
      })
    },

    // 担当者新規作成ダイアログ
    openCreateContactPersonDialog: function () {
      this.contactPersonDialog = true;
      this.contactPersonDialogMode = "store";

      this.contactPersonForm.id = null;
      this.contactPersonForm.name = null;
      this.contactPersonForm.email = null;
      this.contactPersonForm.tel = null;
      this.contactPersonForm.department = null;
      this.contactPersonForm.position = null;
    },

    // 担当者新規作成
    createContactPerson: function () {
      this.contactPersonForm.post(this.$route('contact-persons.store'), {
        preserveScroll: true,
        onStart: () => this.$set(this.loading, "contact-person-create", true),
        onSuccess: () => {
          this.$toasted.show('担当者情報を作成しました');
          this.contactPersonDialog = false;
        },
        onFinish: () => this.$set(this.loading, "contact-person-create", false),
      });
    },

    // 営業エリア更新ダイアログ
    openUpdateBusinessDistrictDialog: function (businessDistrict) {
      this.businessDistrictDialog = true;
      this.businessDistrictDialogMode = "update";

      this.businessDistrictForm.id = businessDistrict.id;
      this.businessDistrictForm.prefecture = businessDistrict.prefecture;
      this.businessDistrictForm.address = businessDistrict.address;
    },

    // 営業エリア更新
    updateBusinessDistrict: function (businessDistrictId) {
      this.businessDistrictForm.put(this.$route('business-districts.update', { business_district: this.businessDistrictForm.id }), {
        preserveScroll: true,
        onStart: () => this.$set(this.loading, "business-district-update", true),
        onSuccess: () => {
          this.$toasted.show('営業エリア情報を更新しました');
          this.businessDistrictDialog = false;
        },
        onFinish: () => this.$set(this.loading, "business-district-update", false),
      })
    },

    // 営業エリア削除
    deleteBusinessDistrict: function () {
      this.$confirm('この営業エリアを削除してよろしいですか？<br>削除した項目を元に戻すことはできません').then(isAccept => {
        if (isAccept) {
          this.businessDistrictDialog = false;

          this.businessDistrictForm.delete(this.$route('business-districts.destroy', { business_district: this.businessDistrictForm.id }), {
            preserveScroll: true,
            onStart: () => this.$set(this.loading, "business-district-delete", true),
            onSuccess: () => this.$toasted.show('営業エリア情報を削除しました'),
            onFinish: () => this.$set(this.loading, "business-district-delete", false),
          });
        }
      })
    },

    // 営業エリア新規作成ダイアログ
    openCreateBusinessDistrictDialog: function () {
      this.businessDistrictDialog = true;
      this.businessDistrictDialogMode = "store";

      this.businessDistrictForm.id = null;
      this.businessDistrictForm.prefecture = null;
      this.businessDistrictForm.address = null;
    },

    // 営業エリア新規作成
    createBusinessDistrict: function () {
      this.businessDistrictForm.post(this.$route('business-districts.store'), {
        preserveScroll: true,
        onStart: () => this.$set(this.loading, "business-district-create", true),
        onSuccess: () => {
          this.$toasted.show('営業エリア情報を作成しました');
          this.businessDistrictDialog = false;
        },
        onFinish: () => this.$set(this.loading, "business-district-create", false),
      });
    },
  }
}
</script>
