import { ComponentFixture, TestBed } from '@angular/core/testing';

import { IngredienteAdmin } from './ingrediente-admin';

describe('IngredienteAdmin', () => {
  let component: IngredienteAdmin;
  let fixture: ComponentFixture<IngredienteAdmin>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [IngredienteAdmin]
    })
    .compileComponents();

    fixture = TestBed.createComponent(IngredienteAdmin);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
