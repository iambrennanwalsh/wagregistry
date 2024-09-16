import { Head } from '@/components/head'
import { PageTitle } from '@/components/pageTitle'
import BaseLayout from '@/layout/baseLayout'
import { Controller } from '@/types'

const Account: Controller = () => {
  return (
    <>
      <Head title="Account" description="Manage your account." />
      <PageTitle title="Account">Manage your account</PageTitle>
    </>
  )
}

Account.layout = page => <BaseLayout children={page} />

export default Account
