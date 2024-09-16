import { Head } from '@/components/head'
import { PageTitle } from '@/components/pageTitle'
import { ForgotForm } from '@/forms/forgotForm'
import BaseLayout from '@/layout/baseLayout'
import { type Controller } from '@/types'
import { Paper } from '@mantine/core'

const Forgot: Controller = () => {
  return (
    <>
      <Head title="Forgot my Password" description="I forgot my password." />
      <PageTitle title="Forgot my Password">
        Enter the email address you signed up with and we'll send you a special link where you can reset your password.
      </PageTitle>
      <Paper withBorder shadow="md" p={30} mt={30} radius="md">
        <ForgotForm />
      </Paper>
    </>
  )
}

Forgot.layout = page => <BaseLayout size={420} children={page} />

export default Forgot
