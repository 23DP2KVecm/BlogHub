import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'
import { createVuetify } from 'vuetify'

const blogLight = {
  dark: false,
  colors: {
    primary: '#1565C0',
    'primary-darken-1': '#003c8f',
    secondary: '#FF6F00',
    'secondary-darken-1': '#c43e00',
    background: '#F8F9FA',
    surface: '#FFFFFF',
    error: '#D32F2F',
    info: '#0288D1',
    success: '#388E3C',
    warning: '#F57C00',
  },
}

const blogDark = {
  dark: true,
  colors: {
    primary: '#42A5F5',
    'primary-darken-1': '#1976D2',
    secondary: '#FFB300',
    'secondary-darken-1': '#FF8F00',
    background: '#121212',
    surface: '#1E1E1E',
    error: '#EF5350',
    info: '#29B6F6',
    success: '#66BB6A',
    warning: '#FFA726',
  },
}

export default createVuetify({
  theme: {
    defaultTheme: 'blogLight',
    themes: { blogLight, blogDark },
  },
})
