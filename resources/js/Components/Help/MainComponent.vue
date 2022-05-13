<template>
  <div>
    <v-card>
      <v-card-title>ヘルプ</v-card-title>
      <v-list>
        <v-list-group>
          <template v-slot:activator>
            <v-list-item-content>
              <v-list-item-title>トレーニング追加</v-list-item-title>
            </v-list-item-content>
          </template>

          <v-list-item>
            <v-card-text>
              当日や指定した日のトレーニングを登録し、後から確認することができます！
            </v-card-text>
          </v-list-item>
        </v-list-group>

        <v-list-group>
          <template v-slot:activator>
            <v-list-item-content>
              <v-list-item-title>トレーニング予定</v-list-item-title>
            </v-list-item-content>
          </template>

          <v-list-item>
            <v-card-text>
            曜日ごとにトレーニングの予定を立てることができます！<br>
            曜日ごとのトレーニングを当日のトレーニングとして登録できます。
            </v-card-text>
          </v-list-item>
        </v-list-group>
      </v-list>

      <v-card-title>利用規約他</v-card-title>

      <v-list>
        <v-list-group>
          <template v-slot:activator>
            <v-list-item-content>
              <v-list-item-title>利用規約</v-list-item-title>
            </v-list-item-content>
          </template>

          <v-list-item>
            <term-of-service-component></term-of-service-component>
          </v-list-item>
        </v-list-group>

        <v-list-group>
          <template v-slot:activator>
            <v-list-item-content>
              <v-list-item-title>プライバシーポリシー</v-list-item-title>
            </v-list-item-content>
          </template>

          <v-list-item>
            <privacy-policy-component></privacy-policy-component>
          </v-list-item>
        </v-list-group>

      </v-list>
    </v-card>
  </div>
</template>

<script>
import TermOfServiceComponent from './Items/TermOfServiceComponent';
import PrivacyPolicyComponent from './Items/PrivacyPolicyComponent';
export default {
  components: {
    TermOfServiceComponent,
    PrivacyPolicyComponent
  }
}
</script>

<style scoped>

</style>