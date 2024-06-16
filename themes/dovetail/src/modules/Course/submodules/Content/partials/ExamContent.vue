<template>
  <div>
    <v-toolbar flat class="sticky mb-6">
      <v-toolbar-title>{{ __('Add Question') }}</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-btn dark color="primary">{{ __('Multiple Choice') }}</v-btn>
    </v-toolbar>

    <div>
      <v-simple-table fixed-header height="600">
        <template v-slot:default>
          <thead>
            <tr>
              <th class="text-left">{{ __('Question') }}</th>
              <th class="text-left">{{ __('Type') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, i) in exams" :key="i">
              <td>{{ item.name }}</td>
              <td>
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-icon v-on="on">{{ item.icon }}</v-icon>
                  </template>
                  <span>{{ item.type }}</span>
                </v-tooltip>
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
    </div>
  </div>
</template>

<script>
  export default {
    data: () => ({
      exams: [
        {
          icon: 'mdi-radiobox-marked',
          name: 'What is the correct answer?',
          type: 'Multiple choice',
        },
        {
          icon: 'mdi-checkbox-marked-outline',
          name: 'What is your favorite subject?',
          type: 'Multiple choice',
        },
      ],
    }),
  }
</script>
