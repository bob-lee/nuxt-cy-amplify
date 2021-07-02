<template>
  <div class="container">
    <div>
      <Logo />
      <h1 class="title">
        Hello "nuxt-cy-amplify"!
      </h1>
      <div class="links">
        <a
          href="https://nuxtjs.org/"
          target="_blank"
          rel="noopener noreferrer"
          class="button--green"
        >
          Documentation
        </a>
        <a
          href="https://github.com/nuxt/nuxt.js"
          target="_blank"
          rel="noopener noreferrer"
          class="button--grey"
        >
          GitHub
        </a>
        <button data-cy="graphql" @click="search3">graphql</button>
        <input data-cy="term" placeholder="Country name" v-model="term" />
        <button data-cy="search" @click="search">Search</button>
        <span data-cy="countries">{{countries.length}}</span>
      </div>
    </div>
  </div>
</template>

<script>
import Axios from 'axios';

const API_URL = 'https://my-graphql-server.com/graphql'

export default {
  data() {
    return {
      term: '',
      countries: []
    }
  },
  methods: {
    search2() {
      fetch('https://restcountries.eu/rest/v2/name/' + this.term)
        .then(response => response.json())
        .then(data => console.log(data));
    },
    async postRevoke(id) { // https://stackoverflow.com/questions/54163952/async-await-in-fetch-how-to-handle-errors
      const data = { client: { id }}
      const response = await fetch('https://8xbpv0j4mb.execute-api.ap-southeast-2.amazonaws.com/v1/revoke-access', {
        method: 'POST',
        // mode: 'no-cors',
        headers: {
          // 'Accept': 'application/json',
          'Content-Type': 'application/json',
          'x-api-key': '7mgOJolrFJ2YHAtqThda48B2XXhl3ngn1JEQxTM4'
        },
        body: JSON.stringify(data)
      })

      return response.json()
    },
    search3() {
      this.postRevoke(-124)
        .then(data => console.log(data))
        .catch((error) => {
          console.error('Error:', error);
        });
      // fetch('https://8xbpv0j4mb.execute-api.ap-southeast-2.amazonaws.com/v1/revoke-access', {
      //   method: 'POST',
      //   // mode: 'no-cors',
      //   headers: {
      //     'Accept': 'application/json',
      //     'Content-Type': 'application/json',
      //     'x-api-key': '7mgOJolrFJ2YHAtqThda48B2XXhl3ngn1JEQxTM4'
      //   },
      //   body: JSON.stringify(data)
      // })
      //   .then(response => response.json())
      //   .then(data => console.log('revoked', data))
      //   .catch(error => {
      //     console.error('fetch error', error)
      //   })
        // .then(response => response.json())
        // .then(data => console.log(data));
    },
    search4() {
      const data = { client: { id: -125 }}
      Axios.post('https://8xbpv0j4mb.execute-api.ap-southeast-2.amazonaws.com/v1/revoke-access',
        data,
        {
          headers: {
            'x-api-key': '7mgOJolrFJ2YHAtqThda48B2XXhl3ngn1JEQxTM4'
          }
        }
      )
        .then(repsonse => console.log('response', response))
        .catch(error => console.log(error.message ?? error));
        // .catch(console.log);
    },
    async search() {
      const response = await fetch('https://restcountries.eu/rest/v2/name/' + this.term)
      this.countries = await response.json()
      console.log('search got', this.term, this.countries)
    },
    async graphql() {
      const { data } = await axios.post(API_URL, {
        query: `query listMtCompanys {
          listMtCompanys {
            mtCompanyId
            companyName
          }
        }`
      }, {
        headers: {
          'Content-Type': 'application/json',
          'x-api-key': 'my-graphql-api-key'
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
