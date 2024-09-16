import { Head } from '@/components/head'
import { PageTitle } from '@/components/pageTitle'
import BaseLayout from '@/layout/baseLayout'
import { type Controller } from '@/types'
import { Link } from '@inertiajs/react'
import { Button, Group } from '@mantine/core'
import classes from './error.module.css'

type ErrorOwnProps = {
  statusCode: string
}

const Error: Controller<ErrorOwnProps> = ({ statusCode }) => {
  const pageTitle =
    {
      503: '503: Service Unavailable',
      500: '500: Server Error',
      404: '404: Page Not Found',
      403: '403: Forbidden'
    }[statusCode] ?? '404: Page Not Found'

  const title =
    {
      503: 'All of our servers are busy.',
      500: 'Something bad just happened..',
      404: 'You have found a secret place.',
      403: 'You shall not pass.'
    }[statusCode] ?? 'You have found a secret place.'

  const description =
    {
      503: 'We cannot handle your request right now, please wait for a couple of minutes and refresh the page. Our team is already working on this issue.',
      500: "Our servers could not handle your request. Don't worry, our development team was already notified. Try refreshing the page.",
      404: 'Unfortunately, this is only a 404 page. You may have mistyped the address, or the page has been moved to another URL.',
      403: 'Sorry, you are forbidden from accessing this page.'
    }[statusCode] ??
    'Unfortunately, this is only a 404 page. You may have mistyped the address, or the page has been moved to another URL.'

  return (
    <>
      <Head title={pageTitle} description={description} />
      <div className={classes.label}>{statusCode}</div>
      <PageTitle title={title}>{description}</PageTitle>
      <Group justify="center">
        <Button component={Link} href="/" variant="subtle" size="md">
          Go back to the home page
        </Button>
      </Group>
    </>
  )
}

Error.layout = page => <BaseLayout children={page} />

export default Error
