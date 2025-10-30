// src/app/admin/user/page.tsx
"use client";

import { useState } from 'react';
import useSWR from 'swr';
import { UserAdminData } from '@/src/types';
import UserForm from '@/src/components/UserForm';

const fetcher = (url: string) => fetch(url).then((res) => res.json());

export default function AdminUserPage() {
  const { 
    data: userList, 
    error, 
    isLoading, 
    mutate 
  } = useSWR<UserAdminData[]>('/api/admin/user', fetcher);

  const [selectedUser, setSelectedUser] = useState<UserAdminData | null>(null);
  const [apiError, setApiError] = useState<string | null>(null);

  const handleEdit = (user: UserAdminData) => {
    setSelectedUser(user);
    setApiError(null);
  };

  const handleDelete = async (id: number) => {
    if (!confirm('Apakah Anda yakin ingin menghapus user ini?')) {
      return;
    }
    try {
      const res = await fetch(`/api/admin/user/${id}`, {
        method: 'DELETE',
      });
      const data = await res.json();
      if (!res.ok) {
        throw new Error(data.error || 'Gagal menghapus');
      }
      mutate();
    } catch (err) {
      if (err instanceof Error) {
        alert(err.message);
      } else {
        alert('Gagal menghapus user');
      }
    }
  };

  const onFormSuccess = () => {
    setSelectedUser(null);
    setApiError(null);
    mutate();
  };

  if (error) return <div>Gagal memuat data</div>;
  if (isLoading) return <div>Loading...</div>;

  return (
    <div className="min-h-screen bg-gray-100 p-8">
      <h1 className="text-3xl font-bold text-gray-800 mb-6">Manajemen User (Kasir/Admin)</h1>
      
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {/* Kolom Kiri: Form */}
        <div className="lg:col-span-1 bg-white p-6 rounded-lg shadow-md">
          <h2 className="text-2xl font-semibold mb-4 text-gray-700">
            {selectedUser ? 'Edit User' : 'Tambah User Baru'}
          </h2>
          <UserForm
            initialData={selectedUser}
            onSuccess={onFormSuccess}
            apiError={apiError}
            setApiError={setApiError}
          />
          {selectedUser && (
            <button
              onClick={() => {
                setSelectedUser(null);
                setApiError(null);
              }}
              className="mt-4 text-sm text-blue-600 hover:underline"
            >
              Batal Edit (Kembali ke mode Tambah)
            </button>
          )}
        </div>

        {/* Kolom Kanan: Tabel */}
        <div className="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
          <h2 className="text-2xl font-semibold mb-4 text-gray-700">Daftar User</h2>
          <table className="w-full text-left text-gray-600">
            <thead className="bg-gray-50 border-b border-gray-200">
              <tr>
                <th className="p-3">Nama Lengkap</th>
                <th className="p-3">Username</th>
                <th className="p-3">Role</th>
                <th className="p-3">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {userList && userList.map((user) => (
                <tr key={user.id} className="border-b border-gray-100 hover:bg-gray-50">
                  <td className="p-3 font-medium">{user.nama}</td>
                  <td className="p-3">{user.username}</td>
                  <td className="p-3 capitalize">{user.role}</td>
                  <td className="p-3">
                    <button
                      onClick={() => handleEdit(user)}
                      className="text-blue-600 hover:text-blue-800 font-medium mr-3"
                    >
                      Edit
                    </button>
                    <button
                      onClick={() => handleDelete(user.id)}
                      className="text-red-600 hover:text-red-800 font-medium"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}