class Changelenghtsagain < ActiveRecord::Migration
  def change
    change_column :transactions, :amount, :decimal, precision: 8, scale: 2
    change_column :transactions, :start_saldo, :decimal, precision: 8, scale: 2
    change_column :transactions, :end_saldo, :decimal, precision: 8, scale: 2
  end
end
