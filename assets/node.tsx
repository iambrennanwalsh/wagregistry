import '@/styles/app.css'
import { createInertiaApp } from '@inertiajs/react'
import createServer from '@inertiajs/react/server'
import '@mantine/core/styles.layer.css'
import '@mantine/notifications/styles.layer.css'
import { renderToString } from 'react-dom/server'

createServer(page =>
  createInertiaApp({
    title: title => `${title} • WagRegistry`,
    page,
    render: renderToString,
    resolve: name => {
      const controllers = import.meta.glob('./controllers/**/*.tsx', { eager: true })
      const notFoundRoute = controllers[`./controllers/frontend/error/error.tsx`]
      const [dir, route] = name.split('.')
      const controller = [`./controllers/${dir}/${route}.tsx`, `./controllers/${dir}/${route}/${route}.tsx`]
      return controller[0] in controllers
        ? controllers[controller[0]]
        : controller[1] in controllers
          ? controllers[controller[1]]
          : notFoundRoute
    },
    setup: ({ App, props }) => <App {...props} />
  })
)
