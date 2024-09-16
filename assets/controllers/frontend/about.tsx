import { Head } from '@/components/head'
import { PageTitle } from '@/components/pageTitle'
import BaseLayout from '@/layout/baseLayout'
import { type Controller } from '@/types'

const About: Controller = () => {
  return (
    <>
      <Head title="About" description="About WagRegistry" />
      <PageTitle title="About">
        WagRegistry is a service animal registry. We provide registration and verification services for over 20
        different types of service and support animals.
      </PageTitle>
    </>
  )
}

About.layout = page => <BaseLayout children={page} />

export default About
