import { Head } from '@/components/head'
import { PageTitle } from '@/components/pageTitle'
import { ResetForm } from '@/forms/resetForm'
import BaseLayout from '@/layout/baseLayout'
import { type Controller } from '@/types'
import { Paper } from '@mantine/core'

const Reset: Controller = () => {
  return (
    <>
      <Head title="Reset Password" description="Reset Password" />
      <PageTitle title="Reset my Password">Reset your accounts password.</PageTitle>
      <Paper withBorder shadow="md" p={30} mt={30} radius="md">
        <ResetForm />
      </Paper>
    </>
  )
}

Reset.layout = page => <BaseLayout size={420} children={page} />

export default Reset
