export type Props<P = {}> = {
  children?: React.ReactNode
  className?: string
} & P

export type Component<P = {}> = (props: Props<P>) => JSX.Element

export type Controller<OwnProps = {}> = Component<PageProps & OwnProps> & {
  layout: (page: React.ReactNode) => JSX.Element
}

export interface PageProps {
  theme: 'light' | 'dark'
  errors: Record<string, string>
  auth: {
    user?: {
      id: string
      name: string
      email: string
      gravatar: string
      emailConfirmation: boolean
    }
  }
  notifications: {
    success?: string[]
    info?: string[]
    warning?: string[]
    danger?: string[]
  }
  [index: string]: unknown
}
