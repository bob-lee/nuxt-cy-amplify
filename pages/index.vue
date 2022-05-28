<template>
  <div class="container">
    <div>
      <Logo />
      <h1 class="title">
        Hello "nuxt-cy-amplify"!
      </h1>
      <div class="links">
        <a
          @click.prevent="graphql"
          href="#"
          target="_blank"
          rel="noopener noreferrer"
          class="button--green"
        >
          Graphql api
        </a>
        <a
          @click.prevent="search3a"
          href="#"
          target="_blank"
          rel="noopener noreferrer"
          class="button--grey"
        >
          Rest api({{$config.canIUse}})
        </a>
        <input data-cy="term" placeholder="Country name" v-model="term" />
        <button data-cy="search" @click="search">Search</button>
        <span data-cy="countries">{{countries.length}}</span>
      </div>
      <MyCard />
    </div>
  </div>
</template>

<script>
import Axios from 'axios';

// const API_URL = 'https://my-graphql-server.com/graphql'

export default {
  data() {
    return {
      term: '',
      countries: []
    }
  },
  computed: {
    CanIUse() { // process.env
      return process.env.CAN_I_USE
    }
  },
  mounted() {
    console.log('mounted', process.env.CAN_I_USE, this.$config.canIUse)
  },
  methods: {
    search2() {
      fetch('https://restcountries.eu/rest/v2/name/' + this.term)
        .then(response => response.json())
        .then(data => console.log(data));
    },
    async postRevoke(id) {
      const data = { client: { id }}
      const response = await fetch(this.$config.api2Url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'x-api-key': this.$config.api2Key
        },
        body: JSON.stringify(data)
      })

      if (response.ok) {
        return response.json()
      } else { // not ok
        if (response.status === 400) {
          const error = new Error(response.status)
          error.response = response
          throw error
        } else {
          throw new Error(`Bad response ${response.status} from server: ${response.statusText}`)
        }
      }
    },
    search3() {
      this.postRevoke(1281928)
        .then(console.log)
        .catch(console.log)
    },
    async search3a() {
      try {
        const response = await this.postRevoke(1281928)
        console.log('response', response)
      } catch (error) {
        if (error.response && error.response.status && error.response.status === 400) {
          const text = await error.response.text()
          console.log('error', error.response.status, text) // e.g. 400, Failed to find portal user from given client id
        } else {
          console.log('error', error)
        }
      }
    },
    search4() {
      const data = { client: { id: -125 }}
      Axios.post(this.$config.api2Url,
        data,
        {
          headers: {
            'x-api-key': this.$config.api2Key
          }
        }
      )
      .then(response => console.log('response', response))
      .catch(error => console.log('error', error.response))
      // const result = await response.json()
    },
    async search() {
      const response = await fetch('https://restcountries.eu/rest/v2/name/' + this.term)
      this.countries = await response.json()
      console.log('search got', this.term, this.countries)
    },
    async graphql() {
      const { data } = await Axios.post(this.$config.apiUrl, {
        query: `query listMtCompanys {
          listMtCompanys {
            mtCompanyId
            companyName
          }
        }`
      }, {
        headers: {
          // 'Accept': 'application/json',
          'Content-Type': 'application/json',
          'x-api-key': this.$config.apiKey
        }
      })
      console.log('graphql got', data)
    },
  }
}
</script>

<style>
.container {
  margin: 0 auto;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
}

.title {
  font-family:
    'Quicksand',
    'Source Sans Pro',
    -apple-system,
    BlinkMacSystemFont,
    'Segoe UI',
    Roboto,
    'Helvetica Neue',
    Arial,
    sans-serif;
  display: block;
  font-weight: 300;
  font-size: 100px;
  color: #35495e;
  letter-spacing: 1px;
}

.subtitle {
  font-weight: 300;
  font-size: 42px;
  color: #526488;
  word-spacing: 5px;
  padding-bottom: 15px;
}

.links {
  padding-top: 15px;
}
</style>
