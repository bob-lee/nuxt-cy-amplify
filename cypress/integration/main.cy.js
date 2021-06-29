const responseStubbed = [
  {
    "name":"New Zealand",
    "topLevelDomain":[".nz"],
    "alpha2Code":"NZ",
    "alpha3Code":"NZL",
    "callingCodes":["64"],
    "capital":"Wellington",
    "altSpellings":["NZ","Aotearoa"],
    "region":"Oceania",
    "subregion":"Australia and New Zealand",
    "population":4697854,
    "latlng":[-41.0,174.0],
    "demonym":"New Zealander",
    "area":270467.0,
    "gini":36.2,
    "timezones":["UTC-11:00","UTC-10:00","UTC+12:00","UTC+12:45","UTC+13:00"],
    "borders":[],
    "nativeName":"New Zealand",
    "numericCode":"554",
    "currencies":[
      {"code":"NZD","name":"New Zealand dollar","symbol":"$"}
    ],
    "languages":[
      {"iso639_1":"en","iso639_2":"eng","name":"English","nativeName":"English"},
      {"iso639_1":"mi","iso639_2":"mri","name":"Māori","nativeName":"te reo Māori"}
    ],
    "translations":{"de":"Neuseeland","es":"Nueva Zelanda","fr":"Nouvelle-Zélande","ja":"ニュージーランド","it":"Nuova Zelanda","br":"Nova Zelândia","pt":"Nova Zelândia","nl":"Nieuw-Zeeland","hr":"Novi Zeland","fa":"نیوزیلند"},
    "flag":"https://restcountries.eu/data/nzl.svg",
    "regionalBlocs":[],
    "cioc":"NZL"
  }
]
const API_URL = 'https://u4dms4idsfc7lnskiiqg4rrezm.appsync-api.ap-southeast-2.amazonaws.com/graphql'
const aliasGraphqlName = (operation, name) => {
  const Capital = name.charAt(0).toUpperCase() + name.slice(1)
  return `alias${operation}${Capital}`
}

const aliasGraphqlQuery = (req, name) => {
  const { body } = req
  if (body.query && body.query.toLowerCase().startsWith(`query ${name.toLowerCase()}`)) {
    const alias = aliasGraphqlName('Query', name)
    console.log('req.alias', alias)
    req.alias = alias
  }
}
const queryName = 'listMtCompanys' // expects response { data: { userByEmail: { items: [] } } }
const aliasName = aliasGraphqlName('Query', queryName)

const responseQueryStubbed = {
  "data":{
    "listMtCompanys":[
      {"mtCompanyId":1,"companyName":"company1"},
      {"mtCompanyId":2,"companyName":"company2"},
    ]
  }
}

describe('main page', () => {
  beforeEach(() => {
    cy.intercept('POST', API_URL, (req) => {
      aliasGraphqlQuery(req, queryName)

      if (req.alias && req.alias === aliasName) {
        req.reply(responseQueryStubbed) // request doesn't hit the server
        // req.reply((res) => { // // request does hit the server
        //   res.body = responseQueryStubbed
        // })
      }
    })
    cy.visit('/') // baseUrl
  })
  it.skip('graphql call', () => {
    cy.get('svg.NuxtLogo').should('exist')

    cy.get('button[data-cy=graphql]').click()
    cy.wait(`@${aliasName}`).its('response.body')
      // .should('not.to.be.null')
      .should((body) => {
        expect(body).not.to.be.null
        expect(body.data.listMtCompanys.length).to.be.eq(responseQueryStubbed.data.listMtCompanys.length)
      })
  })

  it('visits main page', () => {
    cy.get('svg.NuxtLogo').should('exist')
    cy.intercept('https://restcountries.eu/rest/v2/name/*'/*, responseStubbed*/).as('search')
    cy.get('input[data-cy=term]').type('united')
    cy.get('button[data-cy=search]').click()
    cy.wait('@search').its('response.body')
      // .should('not.to.be.null')
      .should((body) => {
        expect(body).not.to.be.null
        expect(body.length).to.be.eq(responseStubbed.length)
      })
  })
})