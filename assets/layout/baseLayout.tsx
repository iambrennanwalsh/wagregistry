import { createNavLinks, navLinks } from '@/components/navLinks/navLinks'
import { Provider } from '@/components/provider'
import { Footer } from '@/partials/footer/footer'
import { Header } from '@/partials/header/header'
import { Main } from '@/partials/main/main'
import { Component, PageProps, Props } from '@/types'
import { router, usePage } from '@inertiajs/react'
import { AppShell, MantineSize } from '@mantine/core'
import { useDisclosure } from '@mantine/hooks'
import { Notifications } from '@mantine/notifications'
import { useEffect } from 'react'

type BaseLayoutProps = Props<{
  size?: 'fluid' | MantineSize | (string & {}) | number
  hero?: React.ReactNode
}>

const BaseLayout: Component<BaseLayoutProps> = ({ hero, size, children }) => {
  const [opened, { toggle, close }] = useDisclosure()

  useEffect(() => {
    router.on('navigate', () => {
      close()
    })
  })

  const { url, props: pageProps } = usePage<PageProps>()
  const { auth, notifications } = pageProps

  const notificationCategories = ['info', 'warning', 'danger', 'success'] as const
  const notificationColors = ['blue', 'yellow', 'red', 'green'] as const

  useEffect(() => {
    notificationCategories.forEach((cat, i) => {
      notifications[cat]?.forEach(msg => {
        Notifications.show({
          message: msg,
          color: notificationColors[i],
          className: `notification ${cat}`
        })
      })
    })
  }, [notifications])

  const mainLinks = createNavLinks(url, navLinks.main)
  const authLinks = auth.user ? createNavLinks(url, navLinks.user) : createNavLinks(url, navLinks.auth)
  const showVerificationBar = auth.user && auth.user.emailConfirmation === false ? true : false
  return (
    <Provider>
      <AppShell
        header={{ height: showVerificationBar ? 90 : 60 }}
        navbar={{
          width: 300,
          breakpoint: 'sm',
          collapsed: { mobile: !opened, desktop: true }
        }}
        footer={{ height: 60 }}>
        <Header
          showVerificationBar={showVerificationBar}
          opened={opened}
          toggle={toggle}
          mainLinks={mainLinks}
          authLinks={authLinks}
        />
        {hero && hero}
        <Main size={size}>{children}</Main>
        <Footer mainLinks={mainLinks} authLinks={authLinks} />
      </AppShell>
    </Provider>
  )
}

export default BaseLayout
