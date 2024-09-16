import { Anchor, createTheme } from '@mantine/core'

export const Theme = createTheme({
  colors: {
    primary: [
      '#C2D6CC',
      '#A9C6B8',
      '#9DBEAE',
      '#85AD9A',
      '#6C9D85',
      '#5A8771',
      '#486b5a',
      '#395648',
      '#31493E',
      '#293D33'
    ]
  },
  other: {
    background: '#F9F6F0'
  },
  primaryColor: 'primary',
  primaryShade: 6,
  components: {
    Anchor: Anchor.extend({
      defaultProps: {
        underline: 'never'
      }
    })
  }
})
