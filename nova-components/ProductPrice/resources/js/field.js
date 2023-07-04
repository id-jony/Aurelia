import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-product-price', IndexField)
  app.component('detail-product-price', DetailField)
  app.component('form-product-price', FormField)
})
