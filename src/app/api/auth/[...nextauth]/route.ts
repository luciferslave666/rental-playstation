// src/app/api/auth/[...nextauth]/route.ts
import NextAuth, { AuthOptions, User } from 'next-auth';
import CredentialsProvider from 'next-auth/providers/credentials';
import prisma from '@/src/lib/prisma';
import bcrypt from 'bcryptjs';
import { JWT } from 'next-auth/jwt';

// Extend tipe NextAuth untuk menambahkan field custom
declare module 'next-auth' {
  interface Session {
    user: {
      id: string;
      name: string;
      username: string;
      role: string;
      email: string;
    };
  }

  interface User {
    id: string;
    username: string;
    role: string;
  }
}

declare module 'next-auth/jwt' {
  interface JWT {
    id: string;
    username: string;
    role: string;
  }
}

export const authOptions: AuthOptions = {
  providers: [
    CredentialsProvider({
      name: 'Credentials',
      credentials: {
        username: { label: 'Username', type: 'text', placeholder: 'Username' },
        password: { label: 'Password', type: 'password' }
      },
      async authorize(credentials) {
        // Validasi input
        if (!credentials?.username || !credentials?.password) {
          throw new Error('Username dan Password wajib diisi');
        }

        try {
          // Cari user berdasarkan username
          const user = await prisma.user.findUnique({
            where: { username: credentials.username },
            select: {
              id: true,
              nama: true,
              username: true,
              password: true,
              role: true,
            }
          });

          // Cek apakah user ditemukan
          if (!user) {
            throw new Error('Username atau password salah');
          }

          // Verifikasi password
          const isPasswordValid = await bcrypt.compare(
            credentials.password,
            user.password
          );

          if (!isPasswordValid) {
            throw new Error('Username atau password salah');
          }

          // Return user data (TANPA password)
          return {
            id: user.id.toString(),
            name: user.nama,
            username: user.username,
            role: user.role,
            email: user.username, // Atau gunakan email jika ada di schema
          };
        } catch (error) {
          // Log error untuk debugging (jangan expose ke user)
          console.error('Authentication error:', error);
          
          // Throw generic error untuk keamanan
          if (error instanceof Error) {
            throw error;
          }
          throw new Error('Terjadi kesalahan saat login');
        }
      }
    })
  ],

  session: {
    strategy: 'jwt',
    maxAge: 30 * 24 * 60 * 60, // 30 hari
  },

  callbacks: {
    async jwt({ token, user, trigger, session }) {
      // Saat user baru login
      if (user) {
        token.id = user.id;
        token.role = user.role;
        token.username = user.username;
      }

      // Jika ada update session (optional)
      if (trigger === 'update' && session) {
        token.name = session.name;
        token.username = session.username;
      }

      return token;
    },

    async session({ session, token }) {
      // Tambahkan data dari token ke session
      if (token && session.user) {
        session.user.id = token.id;
        session.user.role = token.role;
        session.user.username = token.username;
      }
      return session;
    }
  },

  pages: {
    signIn: '/login',
    error: '/login',
  },

  // Secret wajib ada di production
  secret: process.env.NEXTAUTH_SECRET,

  // Debug hanya di development
  debug: process.env.NODE_ENV === 'development',
};

const handler = NextAuth(authOptions);
export { handler as GET, handler as POST };