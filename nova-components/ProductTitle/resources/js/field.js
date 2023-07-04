import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-product-title', IndexField)
  app.component('detail-product-title', DetailField)
  app.component('form-product-title', FormField)
})
