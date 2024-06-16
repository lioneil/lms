<template>
  <v-card class="mb-3">
    <v-card-title class="pb-0">{{ trans('Personal Information') }}</v-card-title>
    <v-card-text>
      <v-row>
        <v-col cols="12" md="4">
          <validation-provider vid="firstname" :name="trans('First name')" rules="required" v-slot="{ errors }">
            <v-text-field
              :dense="isDense"
              :error-messages="errors"
              :label="trans('First name')"
              autofocus
              class="dt-text-field"
              outlined
              prepend-inner-icon="mdi-account-outline"
              v-model="resource.data.firstname"
              >
            </v-text-field>
          </validation-provider>
        </v-col>
        <v-col cols="12" md="4">
          <validation-provider vid="middlename" :name="trans('Middle name')" v-slot="{ errors }">
            <v-text-field
              :error-messages="errors"
              :label="trans('Middle name')"
              class="dt-text-field"
              outlined
              :dense="isDense"
              v-model="resource.data.middlename"
            ></v-text-field>
          </validation-provider>
        </v-col>
        <v-col cols="12" md="4">
          <validation-provider vid="lastname" :name="trans('Last name')" rules="required" v-slot="{ errors }">
            <v-text-field
              :error-messages="errors"
              :label="trans('Last name')"
              class="dt-text-field"
              outlined
              :dense="isDense"
              v-model="resource.data.lastname"
            ></v-text-field>
          </validation-provider>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="6">
          <validation-provider vid="details['birthday']" :name="trans('Birthday')" v-slot="{ errors }">
            <v-text-field
              :error-messages="errors"
              :label="trans('Birthday')"
              prepend-inner-icon="mdi-cake-variant"
              class="dt-text-field"
              outlined
              :dense="isDense"
              v-model="resource.data.details['birthday']"
              >
            </v-text-field>
          </validation-provider>
        </v-col>
        <v-col cols="6">
          <validation-provider vid="details['gender']" :name="trans('Gender')" v-slot="{ errors }">
            <v-select
              :dense="isDense"
              :error-messages="errors"
              :items="resource.gender.items"
              :label="trans('Gender')"
              :prepend-inner-icon="resource.changeGenderIcon(resource.data.details['gender'])"
              class="dt-text-field"
              item-text="key"
              menu-props="offsetY"
              outlined
              return-object
              background-color="selects"
              v-model="resource.data.details['gender']"
              append-icon="mdi-chevron-down"
              >
              <template v-slot:item="{ item }">
                <v-list-item-icon>
                  <v-icon :color="item.color" small v-text="item.icon"></v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title v-html="item.key"></v-list-item-title>
                </v-list-item-content>
              </template>
            </v-select>
          </validation-provider>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12">
          <validation-provider vid="details['marital-status']" :name="trans('Marital Status')" v-slot="{ errors }">
            <v-select
              :dense="isDense"
              :error-messages="errors"
              :items="resource.maritalStatus.items"
              :label="trans('Marital Status')"
              :prepend-inner-icon="resource.changeMaritalStatusIcon(resource.data.details['marital-status'])"
              background-color="selects"
              class="dt-text-field"
              menu-props="offsetY"
              outlined
              return-object
              v-model="resource.data.details['marital-status']"
              append-icon="mdi-chevron-down"
              >
              <template v-slot:item="{ item }">
                <v-list-item-icon>
                  <v-icon :color="item.color" small v-text="item.icon"></v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title v-html="item.text"></v-list-item-title>
                </v-list-item-content>
              </template>
            </v-select>
          </validation-provider>
        </v-col>
      </v-row>
    </v-card-text>

    <account-details></account-details>
  </v-card>
</template>

<script>
import User from '../Models/User'

export default {
  components: {
    AccountDetails: () => import('./AccountDetails'),
  },

  computed: {
    resource: function () {
      return User
    },

    isDense: function () {
      return this.$vuetify.breakpoint.xlAndUp
    },
  },
}
</script>
