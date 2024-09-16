import { Component } from '@/types'
import { Head as InertiaHead, usePage } from '@inertiajs/react'

export interface HeadProps {
  title?: string
  description?: string
  img?: string
  alt?: string
}

const defaultDescription = 'WagRegistry'

export const Head: Component<HeadProps> = ({ title, description = defaultDescription, img, alt, children }) => {
  const { url } = usePage()

  return (
    <InertiaHead>
      <title>{title}</title>
      <meta head-key="description" name="description" content={description} />
      <meta head-key="og-url" property="og:url" content={url} />
      <meta head-key="og-title" property="og:title" content={title} />
      {img && <meta head-key="og-image" property="og:image" content={img} />}
      {alt && <meta head-key="og-image-alt" property="og:image:alt" content={alt} />}
      <meta head-key="og-description" property="og:description" content={description} />
      <meta head-key="twitter-url" name="twitter:url" content={url} />
      <meta head-key="twitter-title" name="twitter:title" content={title} />
      <meta head-key="twitter-description" name="twitter:description" content={description} />
      {img && <meta head-key="twitter-description" name="twitter:image" content={img} />}
      {alt && <meta head-key="twitter-image-alt" name="twitter:image:alt" content={alt} />}
      {children && children}
    </InertiaHead>
  )
}
