import '@/styles/app.css'
import { createInertiaApp } from '@inertiajs/react'
import '@mantine/core/styles.layer.css'
import '@mantine/notifications/styles.layer.css'
import { hydrateRoot } from 'react-dom/client'

createInertiaApp({
  title: title => `${title} • WagRegistry`,
  resolve: name => {
    const controllers = import.meta.glob('./controllers/**/*.tsx', { eager: true })
    const notFoundRoute = controllers[`./controllers/frontend/error.tsx`]
    const [dir, route] = name.split('.')
    const controller = [`./controllers/${dir}/${route}.tsx`, `./controllers/${dir}/${route}/${route}.tsx`]
    return controller[0] in controllers
      ? controllers[controller[0]]
      : controller[1] in controllers
        ? controllers[controller[1]]
        : notFoundRoute
  },
  setup({ el, App, props }) {
    hydrateRoot(el, <App {...props} />)
  }
})
