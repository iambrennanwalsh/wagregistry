import { Component } from '@/types'
import { Box, Text, Title } from '@mantine/core'

interface PageTitleProps {
  title: string
  subtitle?: string
}

type PageTitleComponent = Component<PageTitleProps>

export const PageTitle: PageTitleComponent = ({ title, children }) => {
  return (
    <Box>
      <Title ta="center">{title}</Title>
      {children && (
        <Text c="dimmed" size="sm" ta="center" mt={5}>
          {children}
        </Text>
      )}
    </Box>
  )
}
